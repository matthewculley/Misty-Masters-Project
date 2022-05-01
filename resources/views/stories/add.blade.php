@extends('layouts.app')

@section('title', 'Add a story')

@section('content')

<div id="root" class="container-fluid mx-auto">
        
        <div style="width:90%;" class="mx-auto">
            <div class="row">
                <h1>Add a new story to misty</h1>
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
            </div>
            <div>
                <label for="maxInter" class="form-label">Max interactivity level: @{{ maxInter }}</label>
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
            <div class="row">
            <label for="tagList" class="form-label">Story tags:</label>
                <div class="d-flex justify-content-left flex-wrap">
                    <div v-for="s in allTags" :key="s.tag" class="form-check m-2">
                        <input class="form-check-input" :id="s.tag" type="checkbox" :value="s.tag" v-model="tags"></input>
                        <label class="form-check-label" :for="s.tag"> @{{ s.tag }} </label>
                    </div>
                </div>
            </div>
            <br>
            <h3>Misty Skill Details</h3>
            <div class="form-floating">
                <input id="skillId" class="form-control" type="text" placeholder="Story's unique ID" v-model="skillId"></input>
                <label class="p-3"for="title">Story's unique ID</label>
            </div>
            <div>
                <label for="misty_files" class="form-label">Misty story files:</label>
                <input type="file" class="form-control" placeholder="Misty story zip" id="skill">
            </div>
            <div class="text-center">
                <br>
                <button id="add" class="btn btn-primary form-control" style="width:50%" @click="createStory">Add Story</button>
            </div>
        </div>
</div>
<script>
        var app = new Vue({
            el: "#root",
            data: {
                helpVisible: false,
                toggleHelpText: "Show help",
                title: "",
                desc: "",
                minInter: 1,
                maxInter: 1,
                tags: [],
                allTags: [],
                skillId: "",
                minAge: 1,
                maxAge: 1,
                
            },
            methods: {
                toggleHelp: function () {
                    this.helpVisible = !this.helpVisible;
                    this.toggleHelpText = "Show help";
                    if (this.helpVisible) {this.toggleHelpText = "Hide help";}
                },

                createStory: function () {
                    
                    let formData = new FormData();
                    formData.append("title", this.title);
                    formData.append("description", this.desc);
                    formData.append("min_interactivity", this.minInter);
                    formData.append("max_interactivity", this.maxInter);
                    formData.append("min_suitable_age", this.minAge);
                    formData.append("max_suitable_age", this.maxAge);
                    formData.append("thumb", document.getElementById('thumb').files[0]);
                    formData.append("tags", this.tags);
                    formData.append('unique_id', this.skillId);
                    formData.append("skill", document.getElementById('skill').files[0]);

                    
                    axios.post("/api/stories/createStory", 
                    formData, 
                    {
                        'content-type': 'multipart/form-data'
                    })
                    .then(response => {
                        console.log("created story");
                        console.log(response.response);
                        window.location.href = '/stories';
                    })
                    .catch(response => {
                        console.log(response.response);
                    })
                 





                },

                submit: function() {
                    //add to db
                    this.createStory();
                    //add to misty
                    //cancel any misty running stories
                    this.cancelSkills();

                    //load the story to misty
                    this.loadSkill();
                },

                cancelSkills: function () {
                    console.log("Cancelling skills");
                    returnValue = false;
                    axios.post('http://' + this.ip + '/api/skills/cancel', {
                        skill: null, 
                    })
                    .then( response =>{
                        console.log(response);
                        returnValue = true;
                    })
                    .catch(response => {
                        console.log(response.data);
                    });
                },

                loadSkill: function () {
                    console.log("loading skill to misty");
                    skillId = "";
                    var formdata = new FormData();
                    formdata.append("File", document.getElementById('skillFile').files[0], "[PROXY]");
                    formdata.append("ImmediatelyApply", "true");
                    formdata.append("OverwriteExisting", "true");
                    axios.post('http://' + ip + '/api/skills', formdata, {
                        headers: {
                        'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then( response => {
                        console.log("Uploaded skill");
                        console.log(response);
                        // skillId = response.data['result'];
                    })
                    .catch(response => {
                        console.log(response.data);
                    });

                    return skillId;
                }
                
            },
            mounted() {
                axios.get("/api/tags")
                .then(response=>{
                    this.allTags = Object.values(response.data);
                })
                .catch(response => {
                    console.log(response.data);
                })
            }   
        });            
    </script>
@endsection



        