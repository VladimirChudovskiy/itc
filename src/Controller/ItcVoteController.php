<?php

namespace Drupal\itc\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;


class ItcVoteController extends ControllerBase {
    public function saveVote(){

        $user_data = $this->getUserData();
        $entity_type = $_GET['entity_type'];
        $entity_id = $_GET['entity_id'];
        $user_vote = $_GET['user_vote'];
        $field_name = $_GET['field_name'];


        if($this->userVoteEarly($entity_type, $entity_id, $user_data)){
            return new JsonResponse(["message" => "Hey niger! You are vote earlyer"], 200, ['Content-Type'=> 'application/json']);
        }else{
            //vote_action
            $connection = \Drupal::database();
            $connection->insert('vote_action')
                ->fields([
                    'entity_id' => $entity_id,
                    'entity_type' => $entity_type,
                    'vote' => $user_vote,
                    'uid' => $user_data['uid'],
                    'user_ip' => $user_data['user_ip'],
                    'user_browser' => $user_data['user_browser'],
                ])
                ->execute();

            $connection2 = \Drupal::database();
            $query = $connection2->query("SELECT * FROM `vote_action` WHERE `entity_id`='".$entity_id."' AND `entity_type`='".$entity_type."'");
            $sum = 0;
            $result = $query->fetchAll();
            foreach ($result as $item){
                $sum += intval($item->vote);
            }
            $average = $sum/count($result);

            $entity = \Drupal::entityTypeManager()->getStorage($entity_type)->load($entity_id);
            $entity->set($field_name, intval($average));
            $entity->save();

            return new JsonResponse(["message" => "Hey white! You can vote", 'avg'=>$average, 'fn'=>$field_name], 200, ['Content-Type'=> 'application/json']);
        }


    }

    public function getUserData(){
        $uid = \Drupal::currentUser()->id();

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $browser = $_SERVER['HTTP_USER_AGENT'];

        $user_data = [
            'uid' => $uid,
            'user_ip' => $ip,
            'user_browser' => $browser
        ];

        return $user_data;
    }

    public function userVoteEarly($entity_type, $entity_id, $user_data){


        $connection = \Drupal::database();
        $query = $connection->query("SELECT * FROM `vote_action` WHERE `entity_id`='".$entity_id."' AND `entity_type`='".$entity_type."' AND ((`uid`=".$user_data['uid']." AND `uid`!=0) OR (`uid`=0 AND `user_ip`='".$user_data['user_ip']."'))");
        $result = $query->fetchAll();

        if(count($result)>0){
            return true;
        }else{
            return false;
        }
    }
}