<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;


/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
    
        /////// CONFIG ///////
        $username = env('IG_USERNAME', '');
        $password = env('IG_PASSWORD', '');
        $debug = false;
        $truncatedDebug = true;
        //////////////////////
        
        $ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
        $igUsernameSearch = (request()->ig_username != '') ? request()->ig_username : 'lordguirk';
        $items = collect();
        $instaErrors = null;
        
        try {
            $ig->login($username, $password);
        } catch (\Exception $e) {
            dd('Something went wrong: '.$e->getMessage());
        }
    

        try {
            // Fetch instagram user based off user input
            $userId = $ig->people->getUserIdForName($igUsernameSearch);
        
            try {
                $feed = $ig->timeline->getUserFeed($userId);
                
                // The getPopularFeed() has an "items" property, which we need.
                $items = $feed->getItems();
            
                if (isset($items[0])) {
                    $firstItem_mediaId = $items[0]->getId();
            
                    // To get properties with underscores, such as "device_stamp",
                    // just specify them as camelcase, ie "getDeviceTimestamp" below.
                    $firstItem_device_timestamp = $items[0]->getDeviceTimestamp();
                
                    // You can chain multiple function calls in a row to get to the data.
                    foreach($items as $item) {
                        if(!is_null($item->getImageVersions2())) {
                            $item->hashtags = self::getHashtags($item->caption->text);
                            $item->url = $item->getImageVersions2()->getCandidates()[0]->getUrl();
                        } else {
                            continue;
                        }
                    }
                }
            } catch (\Exception $e) {
                dd('lulz');
                // TODO: Write logic to tell user account is private
            }
        } catch (\Exception $e) {
            $instaErrors = $e->getResponse()->getMessage();
        }
// dd($instaErrors->getMessage());        
        return view('backend.dashboard')
            ->with('items', $items)
            ->with('instaErrors', $instaErrors);
    }
    
    function getHashtags($string) {  
        $hashtags= false;  
        preg_match_all("/(#\w+)/u", $string, $matches);  
            if ($matches) {
                $hashtagsArray = array_count_values($matches[0]);
                $hashtags = array_keys($hashtagsArray);
            }
        return $hashtags;
    }
}
