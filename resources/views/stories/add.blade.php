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
                <input type="text" class="form-control" placeholder="Title" id="title">
            </div>
            <div>
                <label for="desc" class="form-label">Story description</label>
                <textarea class="form-control" placeholder="Description" id="desc"></textarea>
            </div>
            <div>
                <label for="inter" class="form-label">Interactivity level</label>
                <input type="number" class="form-control" placeholder="Interactivity" id="inter">
            </div>
            <div>
                <label for="thumb" class="form-label">Thumbnail Image</label>
                <input type="file" class="form-control" placeholder="Thumbnail image" id="thumb">
            </div>
            <div>
                <label for="misty_files" class="form-label">Misty story files</label>
                <input type="file" class="form-control" placeholder="Misty story zip" id="misty_files">
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
                <br>
                <input type="submit" id="add" class="form-control" placeholder="Add story">
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
                
            },
            methods: {
                toggleHelp: function () {
                    this.helpVisible = !this.helpVisible;
                    this.toggleHelpText = "Show help";
                    if (this.helpVisible) {this.toggleHelpText = "Hide help";}
                    
                }
            },
            mounted() {
                axios.get("/api/tags")
                .then(response=>{
                    this.allTags = Object.values(response.data);
                    console.log(response.data);
                })
                .catch(response => {
                    console.log(response.data);
                })
            }   
        });            
    </script>
@endsection



        