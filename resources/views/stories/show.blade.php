@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>@{{ story.title }} </h3>
    <h4>@{{ story.description}}</h4>
    <ul> 
        <li>Played: @{{ story.times_played }} times</li>
        <li>Average Rating: @{{ averageRating }}</li>
    </ul>
    <h3>Play Story</h3>
    <label>Interactivity: </label>
    <label for="interactivity0">0</label>
    <input type="radio" v-model="interactivity" id="interactivity0" name="interactivity" value="0">
    <label for="interactivity1">1</label>
    <input type="radio" v-model="interactivity" id="interactivity1" name="interactivity" value="1">
    <label for="interactivity2">2</label>
    <input type="radio" v-model="interactivity" id="interactivity2" name="interactivity" value="2">
    <label for="interactivity3">3</label>
    <input type="radio" v-model="interactivity" id="interactivity3" name="interactivity" value="3">
    <label for="interactivity4">4</label>
    <input type="radio" v-model="interactivity" id="interactivity4" name="interactivity" value="4">
    <label for="interactivity5">5</label>
    <input type="radio" v-model="interactivity" id="interactivity5" name="interactivity" value="5">
    <input type="submit" value="Play" @click="playStory"></input>
    <h3>Reviews</h3>
    <div class="addReview">
        <h4>Add Review</h4>
        <textarea type="text" id="input" style="width:100%" v-model="newReview"></textarea>
        <p>Rating</p>
        <label for="rating0">0</label>
        <input type="radio" v-model="newRating" id="rating0" name="rating" value="0">
        <label for="rating1">1</label>
        <input type="radio" v-model="newRating" id="rating1" name="rating" value="1">
        <label for="rating2">2</label>
        <input type="radio" v-model="newRating" id="rating2" name="rating" value="2">
        <label for="rating3">3</label>
        <input type="radio" v-model="newRating" id="rating3" name="rating" value="3">
        <label for="rating4">4</label>
        <input type="radio" v-model="newRating" id="rating4" name="rating" value="4">
        <label for="rating5">5</label>
        <input type="radio" v-model="newRating" id="rating4" name="rating" value="5">
        
        <br><br>
        <input type="submit" @click="createReview">
    </div>
    <ul v-for="review in reviews" :key="review.review">
        <li>@{{ review.review }}</li>
        <li>@{{ review.rating }}</li>
    </ul>

    <h3>Play history</h3>
    <ul v-for="history in histories" :key="history.last_played">
        <li>@{{ history.last_played }}</li>
    </ul>
       
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            story: [],
            reviews: [],
            averageRating: 0,
            newReview: "",
            newRating: 0,
            interactivity: 0,
            histories: {},
        },
        methods: {
            createReview: function() {
                axios.post("/api/stories/" + {{ $story->id }} + "/addReview", 
                {
                    review: this.newReview,
                    rating: this.newRating,
                    story_id: {{ $story->id }},
                })
                .then(response => {
                   
                })
                .catch(response => {
                    console.log(response);
                    console.log(response.response);
                })
            },
            playStory: function() {
                console.log("Playing story: " + "{{ $story->title }}" + ", interactivity level: " + this.interactivity);
                axios.post("/api/stories/" + "{{ $story->id }}" + "/play", 
                {
                    story_id: {{ $story->id }},
                    last_played: new Date().toISOString().slice(0, 19).replace('T', ' '),
                })
                .then(response => {
                })
                .catch(response => {
                    console.log(response.response);
                })


            }
        },
        mounted() {
            axios.get("/api/stories/" + {{ $story->id }})
            .then(response=>{
                this.story = response.data;
            })
            .catch(response => {
                console.log(response.data);
            })

            axios.get("/api/stories/" + {{ $story->id }} + "/reviews")
            .then(response=>{
                this.reviews = Object.values(response.data);
                allReviews = 0;
                for(let i=0; i<this.reviews.length; i++) {
                    allReviews += this.reviews[i].rating;
                }
                this.averageRating = (allReviews / this.reviews.length, 2).toPrecision(2);
            })
            .catch(response => {
                console.log(response.data);
            }),

            axios.get("/api/stories/" + {{ $story->id }} + "/history")
            .then(response=>{
                this.histories = Object.values(response.data);
            })
            .catch(response => {
                console.log(response.response);
            })
        }
    });            
</script>
@endsection