@extends('layouts.app')

@section('title', 'Edit Story')

@section('content')

<div id="root" class="container mx-auto">
    <div class="row container">
    <h1> Edit story </h1>
    <div style="width:50%;" class="mx-auto">
            <h3>Story Details</h3>
            <div>
                <label for="title" class="form-label">Title:</label>
                <input v-model="title" type="text" class="form-control" placeholder="Title" id="title">
            </div>
            <div>
                <label for="desc" class="form-label">Story description:</label>
                <textarea v-model="desc" class="form-control" placeholder="Description" id="desc"></textarea>
            </div>
            <div>
                <label for="inter" class="form-label">Interactivity level: @{{ inter }}</label>
                <input type="range" style="width:100%;" class="form-range" min="0" max="5" id="inter" v-model="inter">
            </div>
            <div>
                <label for="minAge" class="form-label">Minimum suitable age: @{{ minAge }}</label>
                <input type="range" style="width:100%;" class="form-range" min="0" max="15" id="minAge" v-model="minAge">
                <label for="maxAge" class="form-label">Maximum suitable age: @{{ maxAge }}</label>
                <input type="range" style="width:100%;" class="form-range" min="0" max="15" id="maxAge" v-model="maxAge">
            </div>
            <div>
                <label for="thumb" class="form-label">Thumbnail Image:</label>
                <input type="file" class="form-control" placeholder="Thumbnail image" id="thumb">
            </div>
            <div>
                <label for="tagList" class="form-label">Story tags:</label>
                <ul id="tagList" class="list-group list-group-horizontal" >
                    <li v-for="s in allTags" class="list-group-item">
                        <input :id="s.tag" type="checkbox" :value="s.tag" v-model="tags"></input>
                        <label :for="s.tag"> @{{ s.tag }} </label>
                    </li>
                </ul>
            </div>
            <br>
            <h3>Misty Details</h3>
            <div>
                <label for="unique_id" class="form-label">Story's unique ID:</label>
                <input type="text" class="form-control" placeholder="Story's unique ID" v-model="unique_id" id="unique_id">
            </div>
            <div>
                <label for="misty_files" class="form-label">Misty story files:</label>
                <input type="file" class="form-control" placeholder="Misty story zip" id="skill">
            </div>
            <div>
                <br>
                <button id="add" class="btn btn-primary form-control" @click="editStory">Edit Story</button>
            </div>
        </div>
    </div>
</div>
<script>
        var app = new Vue({
            el: "#root",
            data: {
                story: {},
                title: "",
                desc: "",
                inter: 0,
                tags: [],
                allTags: [],
                unique_id: "",
                minAge: 0,
                maxAge: 0,
                thumbnail_path: "",
                skill_path: "", 
                test: [],
            },
            methods: {
               
                editStory: function () {
                    
                    let formData = new FormData();
                    formData.append("id", this.story.id);
                    formData.append("title", this.title);
                    formData.append("description", this.desc);
                    formData.append("min_interactivity", Number(this.inter) - 1);
                    formData.append("max_interactivity", Number(this.inter) + 1);
                    formData.append("min_suitable_age", this.minAge);
                    formData.append("max_suitable_age", this.maxAge);
                    formData.append("tags", this.tags);
                    formData.append('unique_id', this.unique_id);

                    //if no new files 
                    if (document.getElementById('skill').files.length === 0) {
                        console.log("no new skill");
                    } else {
                        formData.append("skill", document.getElementById('skill').files[0]);
                    }

                    if (document.getElementById('thumb').files.length === 0) {
                        console.log("no new thumb");

                    } else {
                        formData.append("thumb", document.getElementById('thumb').files[0]);
                    }
                    
                    for (var value of formData.values()) {
                        console.log(value);
                    }

                    axios.post("/api/stories/editStory", 
                    formData, 
                    {
                        'content-type': 'multipart/form-data'
                    })
                    .then(response => {
                        console.log("edited story");
                        console.log(response);
                    })
                    .catch(response => {
                        console.log(response);
                        console.log(response.response);
                    })
                },    
            },
            mounted() {
                axios.get("/api/stories/" + {{ $story->id }})
                .then(response=>{

                    this.story = response.data;
                    story = this.story;

                    this.title = story.title;
                    this.desc = story.description;
                    this.inter = story.min_interactivity;
                    this.inter = story.min_interactivity;

                    this.unique_id = story.misty_skill_id;
                    this.minAge = story.min_suitable_age;
                    this.maxAge = story.max_suitable_age;
                    this.thumbnail_path = this.story.thumbnail_path;
                    this.skill_path = this.story.skill_path;
                })
                .catch(response => {
                    console.log(response.data);
                }),

                axios.get("/api/tags")
                .then(response=>{
                    this.allTags = Object.values(response.data);
                })
                .catch(response => {
                    console.log(response.data);
                }), 

                axios.get("/api/stories/" + {{ $story->id }} + "/tags")
                .then(response=>{
                    tags = Object.values(response.data);
                    console.log(response);
                    validTags = [];
                    for (let i=0; i<tags.length; i++) {
                        validTags.push(tags[i].tag);
                    }

                    this.tags = validTags;
                })
                .catch(response => {
                    console.log(response.response);
                })
            }
        });            
    </script>
@endsection



        