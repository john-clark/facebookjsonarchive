<?php
//just in case we want line numbers, uncomment below
$count = 0;
// load the file into memory
$readjson = file_get_contents('facebook-jrc3rd/posts/your_posts_1.json');
// decode the file in memory to json
$json_object = json_decode($readjson, true);
// go through the json file and make each section an entry
foreach ($json_object as $json_entry) {
        //flush
        $timestamp = "";
        $updated_time = "";
        $title = "";
        $tags = "";
        $tagged = "";
        $post = "";
        $data = "";
        $attachments = "";
        $media = "";
        $link = "";

        //look for the timestamp
        if (isset($json_entry['timestamp'])) {
                $timestamp = date('m-d-Y H:i',$json_entry['timestamp']);
        } else {
                //strange, they all should have one
                $timestamp = "***NO TIMESTAMP***";
        }
        //look for a title
        if (isset($json_entry['title'])) {
                $title = $json_entry['title'];
        } else {
                //fb is messy
                $title = "";
        }
        //check for tags
        if (isset($json_entry['tags'])) {
                //look for tagged people
                $tags = "(tagged:";
                foreach(($json_entry['tags']) as $tagged) {
                        $tags = $tags.$tagged." ";
                }
                $tags = $tags.")";
        } else {
                //nobody tagged cleanup
                $tags = "";
        }
        //check for data on the main array
        if (isset($json_entry['data'])) {
                //found data
                foreach ($json_entry['data'] as $data) {
                        //look at the data
                        //print_r($data);
                        //check to see if the timestamp had been updated
                        if (isset($data['update_timestamp']))  {
                                $updated_time = date('m-d-Y H:i',$data['update_timestamp']);
                                if ($updated_time == $timestamp) {
                                        //echo "same"
                                } else {
                                        $timestamp = $timestamp." updated: ".$updated_time;
                                }
                        }
                        //see if ther was a post
                        if (isset($data['post'])) {
                                $post = $data['post'];
                        } 
                        //need more work here
                }
        } else {
                $data = "***NO DATA***";
        }
        //check for attachments
        if (isset($json_entry['attachments']) && (!empty($json_entry['attachments']))) {
                foreach ($json_entry['attachments'] as $attachments) {
                        if (isset($attachments['data']) && (!empty($attachments['data']))) {
                        foreach ($attachments['data'] as $attach_data) {
                                if (isset($attach_data['media']['uri']) && (!empty($attach_data['media']['uri']))) {
//                                      print_r($attach_data['media']['uri']);
                                        $nextmedia = "<img src=\"./facebook-jrc3rd/".$attach_data['media']['uri']."\" width=\"100\">";
                                        $media = $nextmedia." ".$media;
                                } else {
                                        //-- no media
                                }
                                if (isset($attach_data['external_context']['url']) && (!empty($attach_data['external_context']['url']))) {
//                                      print_r($attach_data['external_context']['url']);
                                        $tubeid = getYoutube($attach_data['external_context']['url']);
                                        if ($tubeid !== "") {
                                                $thumbURL = 'http://img.youtube.com/vi/'.$tubeid.'/0.jpg';
                                                $link = "<a href=\"".$attach_data['external_context']['url']."\"><img src=\"".$thumbURL."\"width=\"100\"></a>";
                                        } else {
                                                //not youtube print normal
                                                $link = "<a href=\"".$attach_data['external_context']['url']."\">".$attach_data['external_context']['url']."</a>";
                                        }
                                } else {
                                        //-- no external
                                }
                        }
                        } else {
                                //- no data
                        }
                }
        } else {
                //no attachement
        }
        //echo $count." ";
        echo $timestamp." ";
        if ($title) echo "<b>".$title."</b><br>";
        if ($tags) echo $tags."<br>";
        echo $post."<br>";
        if ($link) echo $link."<br>";
        if ($media) echo $media."<br>";

//go to the next item in the object
$count++;
}

function getYoutube($url) {
    $youtube_id = "";
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_-]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))([a-zA-Z0-9_-]+)/i';

    if (preg_match($longUrlRegex, $url, $matches)) {
         $youtube_id = $matches[count($matches) - 1];
    }

    if (preg_match($shortUrlRegex, $url, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }
    return $youtube_id;
}
?>
