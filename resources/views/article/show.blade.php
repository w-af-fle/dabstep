@extends('layout')
@section('content')

@if(session('status'))
  <div class="alert alert-success">{{session('status')}}</div>
@endif

<div class="card" style="width: 68rem;">
  <div class="card-body">
    <h5 class="card-title">{{$article->title}}</h5>
    <p class="card-text">{{App\Models\User::findOrFail($article->user_id)->name}}</p>
  </div>
  <ul class="list-group list-group-flush">
    <li class="list-group-item">{{$article->text}}</li>
  </ul>
  <div class="card-body">
@can('update', $article)
  <div class="btn-toolbar" role="toolbar">
    <a class="btn btn-success" href="/article/{{$article->id}}/edit">Article Edit</a>
    <form action="/article/{{$article->id}}" method="post">
        @csrf
        @method('DELETE')
    <button type="submit" class="btn btn-danger">Article Delete</button>
    </form>
    </div>
@endcan
    </div>
</div>


<h1 class="text-center mt-5">Add new Comments</h1>
<div class="container">
  @if(session('save'))
  <div class="alert  alert-success">
    <p>{{session('save')}}</p>
  </div>
  @endif
<form action="/comment" method=POST  class="mb-3">
    @csrf
    <input type="hidden" name="article_id" value="{{$article->id}}">
  <div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Enter your title">
  </div>
  <div class="mb-3">
    <label for="text" class="form-label">Text</label>
    <textarea type="text" class="form-control" id="text" name="text" placeholder="Your text"></textarea>
  </div>
  
  <button type="submit" class="btn btn-primary">Create comment</button>
</form>
</div>

<h1>Comments</h1>
@foreach($comments as $comment)
<div class="card" style="width: 68rem;">
  <div class="card-body">
    <h5 class="card-title">{{$comment->title}}</h5>
    <p class="card-text">{{App\Models\User::findOrFail($comment->user_id)->name}}</p>
    <div class="btn-toolbar" role="toolbar">
    @can('comment', $comment)
    <a class="btn btn-success me-3" href="/comment/{{$comment->id}}/edit">Comment Edit</a>
    <a class="btn btn-danger me-3" href="/comment/{{$comment->id}}/delete">Comment Delete</a>
    @endcan
    </div>
  </div>
@endforeach
@endsection
