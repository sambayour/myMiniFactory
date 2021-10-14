<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends Controller
{
    public function search($searchQuery, $perPage){

        //http get request to myminifactory search api endpoint
         $response = Http::withHeaders([
                        "x-api-key" => "8a4cc02a-0000-f14a-a815-33da126f240a"
                        ])->get('https://www.myminifactory.com/api/v2/search',
                        [
                            'q' => $searchQuery,
                            'per_page' => $perPage
                        ])->json();
        //count items object in the result
        $totalObjects = count($response['items']); 
        //items object in the result
        $responseObjects = $response['items'];
        $totalCountOfFiles = 0;

        //loop through result to get inner elements
        for($i=0; $i < $totalObjects; $i++){                            
            
            //object array of published_at date
           $objectPublishedAt[] = $responseObjects[$i]['published_at'];

            //object tags array merge
            foreach ($responseObjects[$i]['tags'] as $tags) {
                $tagList[] = $tags ? $tags:null;                                
            }
            //sum of object files total count
            $totalCountOfFiles += $responseObjects[$i]['files']['total_count']; 
        }
        //count number of occurence in array tag list
        $tagListCount = array_count_values($tagList);
        arsort($tagListCount);

        //response collection
        return response([
            "count_of_item_object" =>  $totalObjects,            
            "count_of_result_object" => count($response),
            "average_of_object_files" => $totalCountOfFiles / $totalObjects,
            "oldest_publish_date" => min($objectPublishedAt),
            "latest_publish_date" => max($objectPublishedAt),
            "top_three_tags" => array_slice($tagListCount, 0, 3),
            "data" => $response,
            "status" => 'ok',
            "success" => true,
            "message" => "success"
        ],Response::HTTP_OK);
      
    }
}
