@extends('layout')

@section('content')
	@foreach ($posts as $post)
		<article class="card card-listing-item">
			<h1><a href="/post/{{ $post->slug }}">{{ $post->title }}</a></h1>
		</article>
	@endforeach
@endsection