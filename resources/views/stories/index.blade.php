@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>Stories</h3>
    <input type="text" placeholder="search" v-model="searchTerm"></input>
    <input type="submit" value="Search" @click="search"></input>
    <input type="submit" value="Reset" @click="reset"></input>
    <ul>
        <li v-for="s in stories" :key="s.title"><a :href="/stories/ + s.id">@{{ s.title }}</a></li> 
    </ul>
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            stories: [],
            searchTerm: "",
        },
        methods: {
            search: function() {
                console.log("Searching for: " + this.searchTerm);
            },
            reset: function() {
                console.log("Resetting Search");
            }
        },
        mounted() {
            axios.get("/api/stories")
            .then(response=>{
                this.stories = Object.values(response.data);
            })
            .catch(response => {
                console.log(response.data);
            })
        }
    });            
</script>
@endsection