<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use App\Mail\CommentMail;
use App\Models\Article;


class CommentController extends Controller
{
    public function store(Request $request){
        $request->validate([
            'title' => 'required|min:4',
            'text' => 'required|max:256',
        ]);
        $article = Article::findOrFail($request->article_id);
        $comment = new Comment;
        $comment->title = $request->title;
        $comment->text = $request->text;
        $comment->user_id = auth()->user()->id;
        $comment->article_id = $request->article_id;
        if($comment->save()) Mail::to('mariaorel41@mail.ru')->send(new CommentMail($comment, $article));
        return redirect()->route('article.show', ['article'=>$request->article_id])->with('status','Add comment successfully');
    }

    public function delete(Comment $comment)
    {
        Gate::authorize('comment', $comment);
   
        $comment->delete();
        return redirect()->route('article.show', ['article'=>$comment->article_id])->with('save','Delete comment successfully');

    }


    public function edit(Comment $comment)
    {
        Gate::authorize('comment', $comment);
        return view('comment.edit', ['comment' => $comment]);
// сделать страничку редактирования комментария
    }


    public function update(Comment $comment, Request $request)
    {
        Gate::authorize('comment', $comment);

        $request->validate([
            'title' => 'required|min:4',
            'text' => 'required|max:256',
        ]);

        $comment->title = $request->title;
        $comment->text = $request->text;
        $comment->save();
        
        return redirect()->route('article.show', ['article'=>$comment->article_id])->with('save','Comment update success');;
    }
}