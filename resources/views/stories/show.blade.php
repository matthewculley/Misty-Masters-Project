@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>@{{ story.title }} </h3>
    <h4>@{{ story.description}}</h4>
    <p>Played: @{{ story.times_played }} times</p>
       
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            story: [],
        },
        methods: {
        },
        mounted() {
            axios.get("/api/stories/" + {{ $story->id }})
            .then(response=>{
                this.story = response.data;
            })
            .catch(response => {
                console.log(response.data);
            })
        }
    });            
</script>
@endsection