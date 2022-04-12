@extends('layouts.app')

@section('title', 'Add a story')

@section('content')

<div id="root" class="container mx-auto">
    <div class="row container">
    <h1>Add a new story to misty <button type="button" class="btn btn-outline-primary" @click="toggleHelp">@{{ toggleHelpText }}</button></h1>
        <div id="help" class="row" v-if="helpVisible">
            <h4> How to add a story to Misty</h4>
            <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean luctus metus nec tortor porta, nec sollicitudin tortor finibus. Curabitur sit amet nibh arcu. Morbi semper sollicitudin nibh, bibendum finibus nulla pharetra id. Etiam pharetra, elit eget sollicitudin aliquet, felis risus faucibus purus, eget varius eros ligula sit amet massa. Fusce iaculis turpis ac magna dictum, ultricies commodo lorem dictum. Sed scelerisque, eros ac rutrum iaculis, risus tortor efficitur magna, eget ultricies ante felis ac ipsum. Phasellus pellentesque, massa quis semper laoreet, nulla neque congue ante, sit amet posuere ipsum tellus quis odio. Donec ut lacus sapien. Ut facilisis vitae ligula ut varius. Pellentesque varius molestie tempor. Suspendisse turpis mi, commodo vel ullamcorper et, semper eget turpis. Morbi viverra tortor quis ante tincidunt blandit eget ut felis. </p>
        </div>
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
                <label for="minInter" class="form-label">Interactivity level: @{{ minInter }}</label>
                <input type="range" style="width:100%;" class="form-range" min="0" max="5" id="minInter" v-model="minInter">
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
                <label for="title" class="form-label">Story's unique ID:</label>
                <input type="text" class="form-control" placeholder="Story's unique ID" v-model="skillId" id="skillId">
            </div>
            <div>
                <label for="misty_files" class="form-label">Misty story files:</label>
                <input type="file" class="form-control" placeholder="Misty story zip" id="skill">
            </div>
            <div>
                <br>
                <button id="add" class="btn btn-primary form-control" @click="createStory">Add Story</button>
            </div>
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
                minInter: 0,
                maxInter: 0,
                tags: [],
                allTags: [],
                skillId: "",
                minAge: 0,
                maxAge: 0,
                
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



        