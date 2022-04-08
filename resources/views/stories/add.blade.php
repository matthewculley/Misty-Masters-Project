@extends('layouts.app')

@section('title', 'Add a story')

@section('content')

<div id="root" class="container mx-auto">
    <div class="row container">
    <h1> Create new story </h1>
    <h2>Add a new story to misty <button type="button" class="btn btn-outline-primary" @click="toggleHelp">@{{ toggleHelpText }}</button></h2>
        <div id="help" class="row" v-if="helpVisible">
            <h4> How to add a story to Misty</h4>
            <p> Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean luctus metus nec tortor porta, nec sollicitudin tortor finibus. Curabitur sit amet nibh arcu. Morbi semper sollicitudin nibh, bibendum finibus nulla pharetra id. Etiam pharetra, elit eget sollicitudin aliquet, felis risus faucibus purus, eget varius eros ligula sit amet massa. Fusce iaculis turpis ac magna dictum, ultricies commodo lorem dictum. Sed scelerisque, eros ac rutrum iaculis, risus tortor efficitur magna, eget ultricies ante felis ac ipsum. Phasellus pellentesque, massa quis semper laoreet, nulla neque congue ante, sit amet posuere ipsum tellus quis odio. Donec ut lacus sapien. Ut facilisis vitae ligula ut varius. Pellentesque varius molestie tempor. Suspendisse turpis mi, commodo vel ullamcorper et, semper eget turpis. Morbi viverra tortor quis ante tincidunt blandit eget ut felis. </p>
        </div>
        <form style="width:50%;" class="mx-auto">
            <div>
                <label for="title" class="form-label">Title</label>
                <input v-model="title" type="text" class="form-control" placeholder="Title" id="title">
            </div>
            <div>
                <label for="desc" class="form-label">Story description</label>
                <textarea v-model="desc" class="form-control" placeholder="Description" id="desc"></textarea>
            </div>
            <div>
                <label for="inter" class="form-label">Interactivity level</label>
                <input v-model="inter" type="number" class="form-control" placeholder="Interactivity" id="inter">
            </div>
            <div>
                <label for="thumb" class="form-label">Thumbnail Image</label>
                <input type="file" class="form-control" placeholder="Thumbnail image" id="thumb">
            </div>
            <div>
                <label for="misty_files" class="form-label">Misty story files</label>
                <input type="file" class="form-control" placeholder="Misty story zip" id="skillFile">
            </div>
            <div>
                <label for="tagList" class="form-label">Story tags </label>
                <ul id="tagList" class="list-group list-group-horizontal" >
                    <li v-for="s in allTags" class="list-group-item">
                        <input :id="s.tag" type="checkbox" :value="s.tag" v-model="tags"></input>
                        <label :for="s.tag"> @{{ s.tag }} </label>
                    </li>
                </ul>
            </div>
            <div>
                <label for="title" class="form-label">Misty's IP address</label>
                <input type="text" class="form-control" placeholder="Misty's IP address" id="ip">
            </div>
            <div>
                <label for="title" class="form-label">Story's unique ID</label>
                <input type="text" class="form-control" placeholder="Story's unique ID" id="storyId">
            </div>
            


            <div>
                <br>
                <input type="button" id="add" class="form-control" placeholder="Add story" @click="createStory">
            </div>
        </form>
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
                inter: "",
                tags: [],
                allTags: [],
                ip: "", 
                storyId: "",
                minAge: 5,
                maxAge: 10,
                
            },
            methods: {
                toggleHelp: function () {
                    this.helpVisible = !this.helpVisible;
                    this.toggleHelpText = "Show help";
                    if (this.helpVisible) {this.toggleHelpText = "Hide help";}
                },

                createStory: function () {
                    
                    let data = new FormData();
                    data.append("title", this.title);
                    data.append("description", this.desc);
                    data.append("min_interactivity", this.inter-1);
                    data.append("max_interactivity", this.inter+1);
                    data.append("min_suitable_age", this.minAge);
                    data.append("max_suitable_age", this.maxAge);
                    data.append("file", document.getElementById('thumb').files[0]);

                    console.log(document.getElementById('thumb').files[0].name);
                    
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }
                    }

                    axios.post("/api/stories/add", data, config)
                    .then(response => {
                        console.log("created story");
                        // this.newComment = "";
                        console.log(response);

                    })
                    .catch(response => {
                        console.log(response.response);
                    });
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
                        skillId = response.data['result'];
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



        