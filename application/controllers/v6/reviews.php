<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * 
 * This controller contains the functions related to Reviews at the app end
 * @author Casperon
 *
 * */
class Reviews extends MY_Controller {

    public $loadedLang = array();

    function __construct() {

        parent::__construct();
        $this->load->helper(array('cookie', 'date', 'form', 'email'));
        $this->load->library(array('encrypt', 'form_validation'));
        $this->load->model(array('review_model'));
        $this->load->model(array('app_model'));

        /* Authentication Begin */
        $headers = $this->input->request_headers();
        header('Content-type:application/json;charset=utf-8');
        if (array_key_exists("User-Token", $headers)) {
            $this->user_token = $headers['User-Token'];
            try {
                if (isset($this->user_token) && $this->user_token != '') {
                    $user = $this->app_model->get_selected_fields(USERS, array('login_token' => $this->user_token), array('_id'));
                    if ($user->num_rows() <= 0) {
                        echo json_encode(array("is_dead"=>"Yes")); die;
                    }
                }
            } catch (MongoException $ex) {
                echo $ex->getMessage(); die;
            }
        } else if (array_key_exists("Driver-Token", $headers)) {
            $this->driver_token = $headers['Driver-Token'];
            try {
                if (isset($this->driver_token)) {
                    $driver = $this->app_model->get_selected_fields(DRIVERS, array('sso_token' => $this->driver_token), array('_id'));
                    if ($driver->num_rows() <= 0) {
                        echo json_encode(array("is_dead" => "Yes"));
                        die;
                    }
                }
            } catch (MongoException $ex) {
                echo $ex->getMessage();
                die;
            }
        }
        /*Authentication End*/
    }

    public function get_review_options() {
        $responseArr['status'] = '0';
        try {
            $optionsFor = $this->input->post('optionsFor');
            $ride_id = $this->input->post('ride_id');
            if ($optionsFor != '' && $ride_id!='') {
                $ride_ratting_status = '0';
                $checkRide = $this->app_model->get_selected_fields(RIDES, array('ride_id' => $ride_id), array('driver_review_status','rider_review_status'));
                if ($checkRide->num_rows() == 1) {
                    if($optionsFor=='rider'){
                        if(isset($checkRide->row()->rider_review_status)){
                            if($checkRide->row()->rider_review_status=='Yes'){
                                $ride_ratting_status = '1';
                            }
                        }
                    }
                    if($optionsFor=='driver'){
                        if(isset($checkRide->row()->driver_review_status)){
                            if($checkRide->row()->driver_review_status=='Yes'){
                                $ride_ratting_status = '1';
                            }
                        }
                    }
                }
                    
                $condition = array('option_holder' => $optionsFor, 'status' => 'Active');
                $optionsList = $this->user_model->get_all_details(REVIEW_OPTIONS, $condition);
                if ($optionsList->num_rows() > 0) {
                    $review_opt_arr = array();
                    foreach ($optionsList->result() as $options) {
                        if(is_object($options->option_id)){
                            $option_id = $options->option_id->value;
                        }else{
                            $option_id = $options->option_id;
                        }
                        $review_opt_arr[] = array('option_title' => $options->option_name, 'option_id' =>$option_id);
                    }
                    if (empty($review_opt_arr)) {
                        $review_opt_arr = json_decode("{}");
                    }

                    $responseArr['status'] = '1';
                    $responseArr['ride_ratting_status'] = (string)$ride_ratting_status;
                    $responseArr['optionsFor'] = $optionsFor;
                    $responseArr['total'] = $optionsList->num_rows();
                    $responseArr['review_options'] = $review_opt_arr;
                } else {
                    $responseArr['response'] = $this->format_string('Review options not found', 'review_option_not_found');
                }
            } else {
                $responseArr['response'] = $this->format_string('Some of the parameters are missing', 'some_parameters_missing');
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }

    public function submit_reviews() {
        $responseArr['status'] = '0';
        try {
            $ratingsFor = $this->input->post('ratingsFor');
            $ride_id = $this->input->post('ride_id');
            $ratingsArr = $this->input->post('ratings');
            $comments = (string) $this->input->post('comments');

            if ($ratingsFor != '' && $ride_id != '' && is_array($ratingsArr)) {
                if (count($ratingsArr) > 0 && ($ratingsFor == 'driver' || $ratingsFor == 'rider')) {
                    $rideCond = array('ride_id' => $ride_id);
                    $get_ride_info = $this->review_model->get_selected_fields(RIDES, $rideCond, array('user.id', 'driver.id', 'rider_review_status', 'driver_review_status'));

                    $driversRating = 0;
                    $ridersRating = 0;
                    if (isset($get_ride_info->row()->driver_review_status)) {
                        if ($ratingsFor == 'driver' && ($get_ride_info->row()->driver_review_status == 'Yes')) {
                            $driversRating = 1;
                        }
                    }
                    if (isset($get_ride_info->row()->rider_review_status)) {
                        if ($ratingsFor == 'rider' && ($get_ride_info->row()->rider_review_status == 'Yes')) {
                            $ridersRating = 1;
                        }
                    }

                    if (($ratingsFor == 'driver' && $driversRating == 0) || ($ratingsFor == 'rider' && $ridersRating == 0)) {

                        $user_id = $get_ride_info->row()->user['id'];
                        $driver_id = $get_ride_info->row()->driver['id'];

                        $ratingsArr = array_filter($ratingsArr);
                        $num_of_ratings = 0;
                        $totalRatings = 0;
                        $avg_rating = 0;
                        for ($i = 0; $i < count($ratingsArr); $i++) {
                            $totalRatings = $totalRatings + $ratingsArr[$i]['rating'];
                            $num_of_ratings++;
                        }
                        $avg_rating = number_format(($totalRatings / $num_of_ratings), 2);

                        $ride_dataArr = array('total_options' => $num_of_ratings,
                            'total_ratings' => $totalRatings,
                            'avg_rating' => number_format($avg_rating, 2),
                            'ratings' => $ratingsArr,
                            'comments' => $comments
                        );


                        if ($ratingsFor == 'driver') {
                            $this->review_model->set_to_field(RIDES, $rideCond, array('ratings.' . $ratingsFor => $ride_dataArr, 'driver_review_status' => 'Yes'));
                        } else {
                            $this->review_model->set_to_field(RIDES, $rideCond, array('ratings.' . $ratingsFor => $ride_dataArr, 'rider_review_status' => 'Yes'));
                        }
                        /* Update user rating records*/
                        if ($ratingsFor == 'rider') {
                            $userCond = array('_id' => new \MongoId($user_id));
                            $get_user_ratings = $this->review_model->get_selected_fields(USERS, $userCond, array('avg_review', 'total_review'));

                            $userRateDivider = 1;
                            if (isset($get_user_ratings->row()->avg_review)) {
                                $existUserAvgRat = $get_user_ratings->row()->avg_review;
                                $userRateDivider++;
                            } else {
                                $existUserAvgRat = 0;
                            }

                            if (isset($get_user_ratings->row()->total_review)) {
                                $existTotReview = $get_user_ratings->row()->total_review;
                            } else {
                                $existTotReview = 0;
                            }
                            $userAvgRatings = ($existUserAvgRat + $avg_rating) / $userRateDivider;
                            $userTotalReviews = $existTotReview + 1;

                            $this->review_model->update_details(USERS, array('avg_review' => number_format($userAvgRatings, 2), 'total_review' => $userTotalReviews), $userCond);
                        }
                        /* Update driver rating records*/
                        if ($ratingsFor == 'driver') {
                            $driverCond = array('_id' => new \MongoId($driver_id));
                            $get_driver_ratings = $this->review_model->get_selected_fields(DRIVERS, $driverCond, array('avg_review', 'total_review'));

                            $driverRateDivider = 1;
                            if (isset($get_driver_ratings->row()->avg_review)) {
                                $existDriverAvgRat = $get_driver_ratings->row()->avg_review;
                                if ($get_driver_ratings->row()->avg_review != '') {
                                    $driverRateDivider++;
                                }
                            } else {
                                $existDriverAvgRat = 0;
                            }

                            if (isset($get_driver_ratings->row()->total_review)) {
                                $existDriverTotReview = $get_driver_ratings->row()->total_review;
                            } else {
                                $existDriverTotReview = 0;
                            }
                            $driverAvgRatings = ($existDriverAvgRat + $avg_rating) / $driverRateDivider;
                            $driverTotalReviews = $existDriverTotReview + 1;

                            $this->review_model->update_details(DRIVERS, array('avg_review' => number_format($driverAvgRatings, 2), 'total_review' => $driverTotalReviews), $driverCond);
                        }

                        $responseArr['status'] = '1';
                        $responseArr['response'] = $this->format_string('Your ratings submitted successfully', 'your_ratings_submitted');
                    } else {
                        $responseArr['response'] = $this->format_string('Already you have submitted your ratings for this ride.', 'already_you_submitted_ratings_for_this_ride'); # as a '.$ratingsFor;
                    }
                } else {
                    $responseArr['response'] = $this->format_string('Submitted ratings fields are not valid', 'submitted_ratings_field_invalid');
                }
            } else {
                $responseArr['response'] = $this->format_string('Some of the parameters are missing', 'some_parameters_missing');
            }
        } catch (MongoException $ex) {
            $returnArr['response'] = $this->format_string('Error in connection', 'error_in_connection');
        }
        $json_encode = json_encode($responseArr, JSON_PRETTY_PRINT);
        echo $this->cleanString($json_encode);
    }
}

/* End of file reviews.php */
/* Location: ./application/controllers/mobile/reviews.php */