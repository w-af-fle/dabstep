<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Comment;
use App\Events\ArticleCreateEvent;


class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : 0;
        $articles = Cache::remember('articles_'.$page, 3000, function(){
            return Article::latest()->paginate(5);
        });
        return view('article.index', ['articles' => $articles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', [self::class]);
        return view('article.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $keys = DB::table('cache')->whereRaw('`key` GLOB :key', [':key'=>'articles_*[0-9]'])->get();
        foreach($keys as $key) {
            Cache::forget($key->key);
        }
        $request->validate([
            'date_print'=>'required|date',
            'title'=>'required',
            'text'=>'required',
        ]);

        
        $article = new Article;
        $article->date_print = $request->date_print;
        $article->title = $request->title;
        $article->text = $request->text;
        $article->user_id = auth()->id();
        
        $article->save();
        if ($article->save()) ArticleCreateEvent::dispatch($article);

        return redirect() -> route('article.index');



        
        // return back()->withErrors([
        //     'email' => 'The provided credentials do not match our records.',
        // ])->onlyInput('email');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article, Comment $comment)
    {
        $user = User::findOrFail($article->user_id);
        $comments = Comment::where('article_id',$article->id)->get(); 
        return view('article.show', ['article' => $article, 'user' => $user, 'comments' => $comments]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        Gate::authorize('update', [self::class, $article]);
        


        return view('article.edit', ['article' => $article]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        Gate::authorize('update', [self::class, $article]);

        $request->validate([
            'date_print'=>'required|date',
            'title'=>'required',
            'text'=>'required',
        ]);

        $article->date_print = $request->date_print;
        $article->title = $request->title;
        $article->text = $request->text;
        $article->user_id = auth()->id();
        

        $article->save();

        return redirect()->route('article.show', ['article'=>$article->id])->with('status','Update success');;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {

        Cache::flush();
        Gate::authorize('delete', [self::class, $article]);
        
        $article->delete();
        return redirect()->route('article.index')->with('status','Delete success');

    }
}