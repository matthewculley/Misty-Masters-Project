@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root" class="container-fluid mx-auto border" style="width:90%;">
    <div class="row">
        <h1 style="width:99%;" class="card-title p-3 m-3 border-bottommx-auto ">@{{ story.title }}</h1>

        <div class="col justify-content-center mx-auto">
            <div class="card m-3">
                <img class="card-img-top" v-bind:src="thumbnail_path" style="width:100%">
                <div class="card-body">
                    <p class="card-text"><strong>Description:</strong> @{{ story.description }}</p>
                    <p class="card-text"><strong>Tags:</strong> @{{ tags }}</p>
                    <p class="card-text"><strong>Interactivity range:</strong> @{{ story.min_interactivity  }}-@{{ story.max_interactivity }}</p>
                    <p class="card-text"><strong>Age range:</strong> @{{ story.min_suitable_age  }} - @{{ story.max_suitable_age }} years old.</p>
                    <p class="card-text"><strong>Played:</strong> @{{ story.times_played }} times.</p>
                    <p class="card-text"><strong>Average rating:</strong> @{{ averageRating  }}</p>
                    <p class="card-text"><strong>Misty skill ID:</strong> @{{ story.misty_skill_id  }}</p>
                    <button type="submit" class="btn btn-primary" @click="download">Download skill</button>
                    <button type="submit" class="btn btn-primary" value="Edit" @click="editStory">Edit Story</button>
                </div>
            </div>
        </div>

        <div class="col">

            <div class="row border m-3 p-2">

                <h4>Play Story</h4>
                <div class="form-floating">
                    <input name="ip" v-model="mistyIP" type="text" class="form-control" placeholder="Misty's IP" id="ip">
                    <label class="p-3"for="ip"> Misty's IP Address</label>
                </div>            
                <br>
                <label for="inter">Interactivity: @{{ interactivity }} </label>
                <input type="range" style="width:95%;" class="form-range mx-auto" min="1" max="5" id="inter" v-model="interactivity">
                <div class="text-center">
                    <button type="submit" style="width:75%;" class="btn btn-primary text-center" value="Play" @click="playStory">Play Story</button>
                </div>
                
            </div>
    
            <div class="row border m-3 p-2">
                <h3>Add Review</h3>
                <label for="rating">Rating: @{{ newRating }}</label>
                    <br>
                <input type="range" style="width:95%" class="form-range mx-auto" min="1" max="5" id="rating" v-model="newRating">
                <div class=""> 
                    <div class="form-floating">
                        <textarea class="form-control" type="text" id="input" v-model="newReview" placeholder="Review:" style="height:100px;"></textarea>
                        <label for="input">Review</label>
                    </div>  
                    <br>
                    <div class="text-center">
                        <button type="submit" style="width:75%;" class="btn btn-primary" value="submit" @click="createReview">Submit</button>                   
                    </div>
                </div>
            </div>

        </div>

    </div>
    <div class="row border-top">
        <div class="col-sm-7 m-3 p-2">
            <h2>Reviews</h2>
            <div v-for="review in reviews" :key="review.review" class="border mb-3" >
                <div class="m-3"> 
                    <p><strong>Rating:</strong> @{{ review.rating }}</p>     
                    <p>@{{ review.review }}</p>     
                </div>
            </div> 
        </div>

        <div class="col-sm-4 mx-auto m-3 p-2">
        <h2>Play History</h2>
            <div v-for="history in histories" :key="history.last_played" class="border mb-3">
                <div class="container card-body"> 
                    <p><strong>Played:</strong> @{{ history.last_played }}</p>     
                </div>
            </div> 
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
            histories: [],
            tags: "",
            mistyIP: "",
        },
        methods: {
            toggle: function() {
                this.historyVisible = !this.historyVisible;
                this.commentsVisible = !this.commentsVisible;                
            },

            download: function() {
                window.open("/" + this.story.misty_skill_path, '_blank');              
            },

            toggelFullHistory: function() {
                this.seeFullHistory = !this.seeFullHistory;
            },

            editStory: function() {
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
                    console.log(response);
                   this.updateReviews();
                   this.newRating = 1;
                   this.newReview = "";
                })
                .catch(response => {
                    error = Object.values(response.response)[0];
                    alert("Error " + error.code + ": " + error.message);
                    console.log(response);
                    console.log(response.response);
                })
            },

            updateHistory: function() {
                axios.get("/api/stories/" + {{ $story->id }} + "/history")
                .then(response=>{
                    this.histories = Object.values(response.data);
                })
                .catch(response => {
                    console.log(response.response);
                })
            },

            playStory: function() {
                
                console.log("Playing story: " + "{{ $story->title }}" + ", interactivity level: " + this.interactivity);
                console.log(this.story.misty_skill_path);
                //get the skill file
                skill = null;

                //upload skill to misty
                skillId = "";

                var formdata = new FormData();
                formdata.append("File", skill, "[PROXY]");
                

                axios.post('http://' + this.mistyIP + '/api/skills', formdata, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(function (response) {
                    console.log("Uploaded skill");
                    console.log(response);
                    skillId = response.data['result'];
                })
                .catch(function (error) {
                    console.log(error);
                });

                //play skill on misty
                axios.post('http://' + ip + '/api/skills/start', {
                    skill: this.story.misty_skill_id, 
                    method: 'null'
                })
                .then(function (response) {
                    console.log("Showing emotions")
                    console.log(response);
                })
                .catch(function (error) {
                    console.log(error);
                });      

                          
                //update play counter
                axios.post("/api/stories/" + "{{ $story->id }}" + "/play", 
                {
                    story_id: {{ $story->id }},
                    last_played: new Date().toISOString().slice(0, 19).replace('T', ' '),
                })
                .then(response => {
                    this.updateHistory();
                })
                .catch(response => {
                    console.log(response);
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
            
            axios.get("/api/stories/" + {{ $story->id }} + "/tags")
            .then(response=>{
                tags = Object.values(response.data);
                console.log(response);

                for (let i=0; i<tags.length; i++) {
                    if (i > 0) { 
                        this.tags += ", ";  
                    }

                    this.tags += tags[i].tag;
                    // console.log(tags[i].tag);

                }
            })
            .catch(response => {
                console.log(response.response);
            })

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