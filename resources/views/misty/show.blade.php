@extends('layouts.app')

@section('title', 'Misty')

@section('content')
<div id="root">
    <h3>Story History</h3>

    <ul v-for="story in lastPlayedStories" :key="story.title+story.last_played">
        <li><a :href="/stories/ + story.id">@{{ story.title }}</a></li>
        <li>@{{ story.last_played }}</li>
    </ul>
</div>



<script>
    var app = new Vue({
        el: "#root",
        data: {
            lastPlayedStories: [],
        },
        methods: {
        },
        mounted() {
            axios.get("/api/misty/stories_played")
            .then(response=>{
                this.lastPlayedStories = response.data;
            })
            .catch(response => {
                console.log(response.response);
            })
        }
    });            
</script>

@endsection