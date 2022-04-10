@extends('layouts.app')

@section('title', 'Edit Story')

@section('content')

<div id="root" class="container mx-auto">
    <div class="row container">
    <h1> Edit story </h1>
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
                <input type="button" id="add" class="form-control" placeholder="Add story" @click="editStory">
            </div>
        </form>
    </div>
</div>
<script>
        var app = new Vue({
            el: "#root",
            data: {
                story: {},
                title: "",
                desc: "",
                inter: "",
                tags: [],
                allTags: [],
                ip: "", 
                storyId: "",
                minAge: 5,
                maxAge: 10,
                thumbnail_path: "",
            },
            methods: {
               
                editStory: function () {
                    
                    let data = new FormData();
                    data.append("id", Number(this.story.id));
                    data.append("title", this.title);
                    data.append("description", this.desc);
                    data.append("min_interactivity", this.inter-1);
                    data.append("max_interactivity", this.inter+1);
                    data.append("min_suitable_age", this.minAge);
                    data.append("max_suitable_age", this.maxAge);
                    data.append("file", this.thumbnail_path);
                    data.append("tags", this.tags);

                    
                    const config = {
                        headers: {
                            'content-type': 'multipart/form-data'
                        }   
                    }

                    // php no likey sending files with put 
                    axios.post("/api/stories/edit", data, config)
                    .then(response => {
                        console.log(response);
                        // return user to story details screen
                        window.location.href = '/stories/' + this.story.id;
                    })
                    .catch(response => {
                        console.log(response.response);
                    })
                    
                },  

               
            
            },
            mounted() {
                axios.get("/api/stories/" + {{ $story->id }})
                .then(response=>{
                    this.story = response.data;

                    story = this.story;
                    this.storyId = story.id;
                    this.title = story.title;
                    this.desc = story.description;
                    this.inter = story.min_interactivity;

                    this.thumbnail_path = "/" + this.story.thumbnail_path;


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
                })
            
            }   
        });            
    </script>
@endsection



        