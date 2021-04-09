<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Comment;
use App\User;
use Auth;

class CommentController extends Controller
{
    
    /**
     * Get Comments for pageId
     *
     * @return Comments
     */
    public function index($pageId)
    {
        //
        $comments = Comment::where('page_id',$pageId)->orderBy('created_at','desc')->get();

        $commentsData = [];
        
        
        foreach ($comments as $key) {
            $user = User::find($key->users_id);
            $name = $user->name;
            $replies = $this->replies($key->id);
            $photo = $user->first()->photo_url;
            // dd($photo->photo_url);
            $reply = 0;
            if(sizeof($replies) > 0){
                $reply = 1;
            } 

           
                array_push($commentsData,[
                    "name" => $name,
                    "photo_url" => (string)$photo,
                    "commentid" => $key->id,
                    "comment" => $key->comment,
                    "reply" => $reply,
                    "replies" => $replies,
                    "date" => $key->created_at->toDateTimeString()
                ]);
         
            
        }
        $collection = collect($commentsData);
        return $collection->sortBy('date');
    }

    protected function replies($commentId)
    {
        $comments = Comment::where('reply_id',$commentId)->get();
        $replies = [];
        

        foreach ($comments as $key) {
            $user = User::find($key->users_id);
            $name = $user->name;
            $photo = $user->first()->photo_url;

            
        
            
                
                array_push($replies,[
                    "name" => $name,
                    "photo_url" => $photo,
                    "commentid" => $key->id,
                    "comment" => $key->comment,
                    "date" => $key->created_at->toDateTimeString()
                ]);
            
            
        
        }
        
        $collection = collect($replies);
        return $collection->sortBy('date');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
        'comment' => 'required',
        'reply_id' => 'filled',
        'page_id' => 'filled',
        'users_id' => 'required',
        ]);
        $comment = Comment::create($request->all());
        // dd($comment); 
        if($comment)
            return [ "status" => "true","commentId" => $comment->id ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $commentId
     * @param  $type
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}