<?php

require_once __DIR__ .  '/../vendor/autoload.php';

/**
*
* Very quick script, to seach github users according to their language, location 
* and download inside a MySQL table
*/

class GetGitUsers {

    public function __construct(){
        //modify these params-------------
        $this->test_mode = 0;
        $this->db = new MysqliDb ('yourlocalhost', 'yourdbuser', 'yourdbpass', 'yourdbname');
        $language = 'php';
        $location = 'mumbai';
        $sort = 'followers';
        //--------------------------------
        $order='desc';
        $this->interval = 60 * 35;//in seconds
        $this->records_per_page = 25;
        $this->start_page = 1;//in seconds
        $this->url = 'https://api.github.com/search/users';
        $this->q = "?q=language:$language+location:$location&sort=$sort&order=$order";
        //example
        //$q = '?q=language:php+location:mumbai&sort=followers&order=desc&page=2';
    }

    public function run(){

        //make the first request
        $results = $this->getRequestData($this->start_page);

        //get page count
        $total_pages = round($results['total_count'] / $this->records_per_page,0);
        print "total_pages($total_pages)\n"; 

        for($i=$this->start_page;$i<=$total_pages;$i++){

            //respect the api rate limit
            //if($i != $this->start_page){
            if(true){
                $start_time = time();
                while(1){    
                    if((time() - $start_time) > $this->interval) break;
                    print "Waiting remaining(".($this->interval - (time() - $start_time)) .") seconds.. \n";
                    sleep(5);
                }
            }

            print "Getting data for page($i)\n";
            if($i != $this->start_page ) $results = $this->getRequestData($i);
            foreach($results['items'] as $user){

                //get the user data
                $user_details = $this->getUserData($user['url']);

                //save the user data
                $this->saveUserData($user_details);
            }
        }

    }

    //helper functions
    private function saveUserData($user_details){    
        //insert into the db
        $id = $this->db->insert ('users', $user_details);
        if($id){
            echo 'user was created. Id=' . $id."\n"; 
        }
        else{
            echo('user creation failed. Userdetails('.json_encode($user_details).')'."\n"); 
        }
    }

    private function removeNotMatchingKeys($hash=array(),$keys=array()){
        foreach($hash as $key => $value){
            if(!in_array($key,$keys)){
                unset($hash[$key]);
            } 
        }
        return $hash;
    }

    private function getUserData($url){
        print "Getting data from user url($url)\n"; 
        if(!$this->test_mode){
            $user_request = Requests::get($url,array(),array());
            $user_details = json_decode($user_request->body,true);
        }
        else{
            $user_details = json_decode(trim(file_get_contents('user.example.json')),true);
        }
        //remove the keys which are not columns
        $cols = array(  'id' ,
                         'login' ,
                         'url' ,
                         'site_admin' ,
                         'name' ,
                         'company' ,
                         'blog' ,
                         'location' ,
                         'email' ,
                         'hireable' ,
                         'bio' ,
                         'public_repos' ,
                         'public_gists' ,
                         'followers' ,
                         'following' ,
                         'created_at' ,
                         'updated_at' ,
                         'score'
        );
        $user_details = $this->removeNotMatchingKeys($user_details,$cols);
        return $user_details;
    }

    private function getRequestData($page=1){
        if(!$this->test_mode){
            $request = Requests::get($this->url . $this->q . "&page=$page", array(),array());
            //example
            //$headers = array('Accept' => 'application/json');
            //$options = array('auth' => array('user', 'pass'));
            //$request = Requests::get('https://api.github.com/search/users?q=language:php+location:mumbai&sort=followers&order=desc&page=2', array(),array());
            $request_body = $request->body;
        }
        else {
            //print "request_url(".$this->url . $this->q.")\n"; 
            $request_body = trim(file_get_contents('results.example.json'));
            //print "request_body($request_body)\n"; 
        }
        if(!($results = json_decode($request_body,true))){
            die("No data returned or data not in a proper json request_body($request_body)");
        }
        print "total records found({$results['total_count']})\n";
        return $results;
    }
} 
