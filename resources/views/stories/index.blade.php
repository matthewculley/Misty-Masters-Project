@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>Stories</h3>
        <ul>
            <li v-for="s in stories" :key="s.title"><a :href="/stories/ + s.id">@{{ s.title }}</a></li> 
        </ul>
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            stories: [],
        },
        methods: {
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