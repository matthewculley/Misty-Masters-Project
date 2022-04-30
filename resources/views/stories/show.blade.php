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
                <p><strong>Tags:</strong> @{{ tags }}</p>
                <p><strong>Interactivity range:</strong> @{{ story.min_interactivity  }}-@{{ story.max_interactivity }}</p>
                <p><strong>Age range:</strong> @{{ story.min_suitable_age  }} - @{{ story.max_suitable_age }} years old.</p>
                <p><strong>Played:</strong> @{{ story.times_played }} times.</p>
                <p><strong>Average rating:</strong> @{{ averageRating  }}</p>
                <p><strong>Misty skill ID:</strong> @{{ story.misty_skill_id  }}</p>

                <button type="submit" class="btn btn-outline-primary" @click="download">Download skill</button>

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
                        <label for="ip" class="form-label">Mist's IP address:</label>
                        <input v-model="mistyIP" type="text" class="form-control" placeholder="Misty's IP" id="ip">
                        <label for="inter">Interactivity: @{{ interactivity }} </label>
                        <input type="range" style="width:100%;" class="form-range" min="1" max="5" id="inter" v-model="interactivity">
                        <br>
                        <button type="submit" class="btn btn-outline-primary" value="Play" @click="playStory">Play Story</button>
                    </div>       
                    <div class="col">   
                        <h5>Edit Story</h5>
                        <button type="submit" class="btn btn-outline-primary" value="Edit" @click="editStory">Edit Story</button>
                        <br>
                        <br>
                        <h5>Toggle Comments / Play History</h5>        
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

                //get the skill file
                skill = null;






                //upload skill to misty
                skillId = "";

                var formdata = new FormData();
                formdata.append("File", skill, "[PROXY]");
                formdata.append("ImmediatelyApply", "true");
                formdata.append("OverwriteExisting", "true");

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