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
    <label for="0">0</label>
    <input type="radio" v-model="interactivity" id="0" name="interactivity" value="0">
    <label for="1">1</label>
    <input type="radio" v-model="interactivity" id="1" name="interactivity" value="1">
    <label for="2">2</label>
    <input type="radio" v-model="interactivity" id="2" name="interactivity" value="2">
    <label for="3">3</label>
    <input type="radio" v-model="interactivity" id="3" name="interactivity" value="3">
    <label for="4">4</label>
    <input type="radio" v-model="interactivity" id="4" name="interactivity" value="4">
    <label for="5">5</label>
    <input type="radio" v-model="interactivity" id="4" name="interactivity" value="5">
    <input type="submit" value="Play" @click="playStory"></input>
    <h3>Reviews</h3>
    <div class="addReview">
        <h4>Add Review</h4>
        <textarea type="text" id="input" style="width:100%" v-model="newReview"></textarea>
        <p>Rating</p>
        <label for="0">0</label>
        <input type="radio" v-model="newRating" id="0" name="rating" value="0">
        <label for="1">1</label>
        <input type="radio" v-model="newRating" id="1" name="rating" value="1">
        <label for="2">2</label>
        <input type="radio" v-model="newRating" id="2" name="rating" value="2">
        <label for="3">3</label>
        <input type="radio" v-model="newRating" id="3" name="rating" value="3">
        <label for="4">4</label>
        <input type="radio" v-model="newRating" id="4" name="rating" value="4">
        <label for="5">5</label>
        <input type="radio" v-model="newRating" id="4" name="rating" value="5">
        
        <br><br>
        <input type="submit" @click="createReview">
    </div>
    <ul v-for="review in reviews" :key="review.review">
        <li>@{{ review.review }}</li>
        <li>@{{ review.rating }}</li>
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
                    this.newReview = "";
                    this.newRating = 0;
                    this.reviews.push(response.data);
                })
                .catch(response => {
                    console.log(response);
                    console.log(response.response);
                })
            },
            playStory: function() {
                console.log("Playing story: " + "{{ $story->title }}" + ", interactivity level: " + this.interactivity);

                //TODO post request to add to recently played stories


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
            })
        }
    });            
</script>
@endsection