@extends('layouts.app')

@section('title', 'Stories')

@section('content')
    <div id="root" >
        <div class="mx-auto card d-flex flex-wrap m-1 " style="width:50%;">
            <div class="card-header bg-light"> 
                <h3> @{{ story.title }} </h1>
            </div>
            <div class="container card-body"> 
                <img v-bind:src="thumbnail_path" class="img-thumbnail" style="width:100%;">
                <br><br>
                <p><strong>Description:</strong> @{{ story.description }}</p>
                <p><strong>Played:</strong> @{{ story.times_played }}.</p>
                <p><strong>Average rating:</strong> @{{ averageRating  }}</p>
            </div>
        </div> 
        <div class="row mx-auto" style="width:50%;">
            <div class="col mx-auto card d-flex flex-wrap m-1 " style="width:50%;">
                <div class="card-header bg-light"> 
                    <h3>Play Story</h3>
                </div>
                <div class="container card-body"> 
                    <label for="inter">Interactivity: @{{ interactivity }} </label>
                    <br>
                    <input type="range" style="width:50%;" class="form-range" min="0" max="5" id="inter" v-model="interactivity">
                    <br>
                    <button type="submit" class="btn btn-outline-primary" value="Play" @click="playStory">Play Story</button>
                </div>
            </div> 
            <div class="col mx-auto card d-flex flex-wrap m-1 " style="width:50%;">
                <div class="card-header bg-light"> 
                    <h3>Add Review</h3>
                </div>
                <div class="container card-body"> 
                    <label for="input">Review: </label>
                    <br>
                    <textarea type="text" id="input" style="width:100%" v-model="newReview"></textarea>
                    <br>
                    <label for="rating">Rating: @{{ newRating }}</label>
                    <br>
                    <input type="range" style="width:100%" class="form-range" min="0" max="5" id="rating" v-model="newRating">
                    <br>
                    <input type="submit" @click="createReview">               
                </div>
            </div> 
        </div>
        
        <!-- <ul v-for="review in reviews" :key="review.review">
            <li>@{{ review.review }}</li>
            <li>@{{ review.rating }}</li>
        </ul> -->
        <div class="mx-auto" style="width:50%;">
            <div v-for="review in reviews" :key="review.review" class="mx-auto card d-flex flex-wrap m-1" >
                <div class="container card-body"> 
                    <p><strong>Review:</strong> @{{ review.review }}</p>     
                    <p><strong>Rating:</strong> @{{ review.rating }}</p>     
                </div>
            </div> 
        </div>

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
            thumbnail_path: "",
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
                   this.updateReviews();
                   this.newRating = 0;
                   this.newReview = "";
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
            },

            updateReviews: function () {
                axios.get("/api/stories/" + {{ $story->id }} + "/reviews")
                .then(response=>{
                    this.reviews = Object.values(response.data);
                    this.reviews.reverse();
                    allReviews = 0;
                    for(let i=0; i<this.reviews.length; i++) {
                        allReviews += this.reviews[i].rating;
                    }
                    this.averageRating = (allReviews / this.reviews.length).toPrecision(2);
                })
                .catch(response => {
                    console.log(response.data);
                })
            }
        },
        mounted() {
            axios.get("/api/stories/" + {{ $story->id }} + "/reviews")
            .then(response=>{
                this.reviews = Object.values(response.data);
                this.reviews.reverse();

                this.calculateAverageRating();
            })
            .catch(response => {
                console.log(response.data);
            }),
            
            axios.get("/api/stories/" + {{ $story->id }})
            .then(response=>{
                this.story = response.data;
                this.thumbnail_path = "/" + this.story.thumbnail_path;

                allReviews = 0;
                    for(let i=0; i<this.reviews.length; i++) {
                        allReviews += this.reviews[i].rating;
                    }
                    this.averageRating = (allReviews / this.reviews.length).toPrecision(2);
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