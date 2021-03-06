@extends('layouts.app')

@section('title', 'Edit Story')

@section('content')

<div id="root" class="container mx-auto">
@if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
    <div style="width:90%;" class="mx-auto">
        <div class="row">
            <h1>Edit story</h1>
        </div>
        <h3>Story Details</h3>
        <div class="form-floating">
            <input id="title" class="form-control" type="text" placeholder="Title" v-model="title"></input>
            <label class="p-3"for="title">Title</label>
        </div>
        <br>
        <div class="form-floating">
            <textarea class="form-control" type="text" id="desc" v-model="desc" placeholder="Description" style="height:100px;"></textarea>
            <label for="desc">Description</label>
        </div> 
        <div>
            <label for="minInter" class="form-label">Minimum interactivity level: @{{ minInter }}</label>
            <input type="range" style="width:100%;" class="form-range" min="1" max="5" id="minInter" v-model="minInter">
            <label for="inter" class="form-label">Maximum interactivity level: @{{ maxInter }}</label>
            <input type="range" style="width:100%;" class="form-range" min="1" max="5" id="maxInter" v-model="maxInter">
        </div>
        <div>
            <label for="minAge" class="form-label">Minimum suitable age: @{{ minAge }}</label>
            <input type="range" style="width:100%;" class="form-range" min="1" max="15" id="minAge" v-model="minAge">
            <label for="maxAge" class="form-label">Maximum suitable age: @{{ maxAge }}</label>
            <input type="range" style="width:100%;" class="form-range" min="1" max="15" id="maxAge" v-model="maxAge">
        </div>
        <div>
            <label for="thumb" class="form-label">Thumbnail Image:</label>
            <input type="file" class="form-control" placeholder="Thumbnail image" id="thumb">
        </div>
        <label for="tagList" class="form-label">Story tags:</label>
        <div class="d-flex justify-content-left flex-wrap">
            <div v-for="s in allTags" :key="s.tag" class="form-check m-2">
                <input class="form-check-input" :id="s.tag" type="checkbox" :value="s.tag" v-model="tags"></input>
                <label class="form-check-label" :for="s.tag"> @{{ s.tag }} </label>
            </div>
        </div>
        <br>
        <h3>Misty Details</h3>
        <div class="form-floating">
                <input id="unique_id" class="form-control" type="text" placeholder="Story's unique ID" v-model="unique_id"></input>
                <label class="p-3"for="unique_id">Story's unique ID</label>
            </div>
        <div>
            <label for="misty_files" class="form-label">Misty story files:</label>
            <input type="file" class="form-control" placeholder="Misty story zip" id="skill">
        </div>
        
        <div v-if="errors">
            <br>
            <div v-for="(v, k) in errors" :key="k">
                <p v-for="error in v" :key="error" class="alert alert-danger">
                    @{{ error }}
                </p>
            </div>
        </div>


        <div class="text-center">
            <br>
            <button id="add" class="btn btn-primary form-control" @click="editStory" style="width:50%;">Edit Story</button>
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
                minInter: 01,
                maxInter: 01,
                tags: [],
                allTags: [],
                unique_id: "",
                minAge: 1,
                maxAge: 1,
                thumbnail_path: "",
                skill_path: "", 
                test: [],
                errors: "",
            },
            methods: {
               
                editStory: function () {
                    
                    let formData = new FormData();
                    formData.append("id", this.story.id);
                    formData.append("title", this.title);
                    formData.append("description", this.desc);
                    formData.append("min_interactivity", Number(this.minInter));
                    formData.append("max_interactivity", Number(this.maxInter));
                    formData.append("min_suitable_age", this.minAge);
                    formData.append("max_suitable_age", this.maxAge);
                    formData.append("tags", this.tags);
                    formData.append('misty_skill_id', this.unique_id);

                    //if no new files 
                    if (!document.getElementById('skill').files.length === 0) {
                        formData.append("skill", document.getElementById('skill').files[0]);
                    }

                    if (!document.getElementById('thumb').files.length === 0) {
                        formData.append("thumb", document.getElementById('thumb').files[0]);
                    }

                    axios.post("/api/stories/editStory", 
                    formData, 
                    {
                        'content-type': 'multipart/form-data'
                    })
                    .then(response => {
                        console.log("edited story");
                        console.log(response);
                        window.open("/stories/" + this.story.id);   
                    })
                    .catch(response => {
                        console.log(response);
                        console.log(response.response.data.errors);
                        this.errors = response.response.data.errors;
                    
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
                    this.minInter = story.min_interactivity;
                    this.maxIinter = story.min_interactivity;

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



        