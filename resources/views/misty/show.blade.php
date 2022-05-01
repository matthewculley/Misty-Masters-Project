@extends('layouts.app')

@section('title', 'Misty')

@section('content')
<div id="root" class="container-fluid" style="width:90%;">
    <h3>Stories Played</h3>

    <div class="d-flex justify-content-between flex-wrap">
        <div v-for="story in lastPlayedStories" :key="story.title+story.last_played" class="card m-1" style="width:250px">
            <img class="card-img-top" v-bind:src="story.thumbnail_path" style="width:100%">
            <div class="card-body">
            <h4 class="card-title">@{{ story.title }}</h4>
            <p class="card-text">Last played: @{{ story.last_played }}</p>
            <!-- <a :href="/stories/ + s.id" class="btn btn-primary">Select</a> -->
            </div>
        </div>
    </div>


    <!-- <div class="row">
        <div v-for="story in lastPlayedStories" :key="story.title+story.last_played" class="border mb-3">
            <div class="container card-body"> 
                <p><strong>Played:</strong> @{{ history.last_played }}</p>     
            </div>
        </div> 
    </div> -->

    <!-- <ul v-for="story in lastPlayedStories" :key="story.title+story.last_played">
        <li><a :href="/stories/ + story.id">@{{ story.title }}</a></li>
        <li>@{{ story.last_played }}</li>
    </ul> -->
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