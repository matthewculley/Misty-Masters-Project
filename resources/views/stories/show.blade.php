@extends('layouts.app')

@section('title', 'Stories')

@section('content')
    <div id="root" >
        <div class="mx-auto card d-flex flex-wrap m-1 " style="width:50%;">
            <div class="card-header bg-light"> 
                <h3> @{{ story.title }} </h3>
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
                    <h3>Story Options</h3>
                </div>
                <div class="row container card-body"> 
                    <div class="col">
                        <h4>Play Story</h4>
                        <label for="inter">Interactivity: @{{ interactivity }} </label>
                        <br>
                        <input type="range" style="width:100%;" class="form-range" min="1" max="5" id="inter" v-model="interactivity">
                        <br>
                        <button type="submit" class="btn btn-outline-primary" value="Play" @click="playStory">Play Story</button>
                    </div>       
                    <div class="col">           
                        <h5>Edit Story</h5>
                        <button type="submit" class="btn btn-outline-primary" value="Edit" @click="editStory">Edit Story</button>
                    </div>
                    <div class="col">   
                        <h4>Toggle Comments / Play History</h4>        
                        <button type="submit" class="btn btn-outline-primary" value="Edit" @click="toggle">Toggle</button>
                    </div>
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
                    <input type="range" style="width:100%" class="form-range" min="1" max="5" id="rating" v-model="newRating">
                    <br>
                    <button type="submit" class="btn btn-outline-primary" value="submit" @click="createReview">Submit</button>                   
                </div>
            </div> 
        </div>
        
        <div class="container">

</div>
        <div v-if="commentsVisible" class="mx-auto" style="width:50%;">
            <div v-for="review in reviews" :key="review.review" class="mx-auto card d-flex flex-wrap m-1" >
                <div class="container card-body"> 
                    <p><strong>Review:</strong> @{{ review.review }}</p>     
                    <p><strong>Rating:</strong> @{{ review.rating }}</p>     
                </div>
            </div> 
        </div>

        <div v-if="historyVisible" class="mx-auto" style="width:50%;">
            <p><strong>Last played:</strong> @{{ histories[0].last_played }}</p>     
            <button type="submit" class="btn btn-outline-primary" value="Play" @click="toggelFullHistory">Show full history</button>


            <div v-if="seeFullHistory" v-for="history in histories" :key="history.last_played" class="mx-auto card d-flex flex-wrap m-1" >
                <div class="container card-body"> 
                    <p><strong>Played:</strong> @{{ history.last_played }}</p>     
                </div>
            </div> 
        </div>

    
       
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            historyVisible: false,
            seeFullHistory: false,
            commentsVisible: true,  
            story: [],
            thumbnail_path: "",
            reviews: [],
            averageRating: 0,
            newReview: "",
            newRating: 1,
            interactivity: 1,
            histories: {},
        },
        methods: {
            toggle: function() {
                this.historyVisible = !this.historyVisible;
                this.commentsVisible = !this.commentsVisible;                
            },

            toggelFullHistory: function() {
                this.seeFullHistory = !this.seeFullHistory;
            },

            editStory: function(){
                window.location.href = '/stories/edit/' + this.story.id;
            },

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