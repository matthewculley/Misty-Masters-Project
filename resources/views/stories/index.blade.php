@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>Stories</h3>
    <h5>Search by title</h5>
    <input type="text" placeholder="search" v-model="searchTerm"></input>
    <input type="submit" value="Search" @click="search"></input>
    <br>
    <h5>Or by keywords</h5>
    <div v-for="s in tags">
        <input :id="s.tag" type="checkbox" :value="s.tag" v-model="checkedTags"></input>
        <label :for="s.tag">@{{ s.tag }}</label>
    </div>
    <input type="submit" value="Apply Keywords" @click="filterTags"></input>
    <h5>Reset search and keywords</h5>
    <input type="submit" value="Reset" @click="reset"></input>

    <ul>
        <li v-for="s in displayStories" :key="s.title"><a :href="/stories/ + s.id">@{{ s.title }}</a></li> 
    </ul>
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            stories: [],
            displayStories: [],
            tags: [],
            searchTerm: "",
            checkedTags: [],
            returnData: [],
        },
        methods: {
            search: function() {
                this.reset();
                this.displayStories = [];

                var closeMatchesDict = {};

                s1 = this.searchTerm.toLowerCase();
            
                for(let i=0; i< this.stories.length; i++) {
                    var similarityIndex = 0.0;
                    var story = this.stories[i];
                    var s2 = story.title.toLowerCase();
                    var len1 = s1.length;
                    var len2 = s2.length;

                    // Maximum distance of shared characters allowed
                    var maxDist = Math.floor(Math.max(len1, len2) / 2) - 1;
                    var matches = 0;

                    var hash1 = Array(s1.length).fill(0);
                    var hash2 = Array(s2.length).fill(0);

                    for (let j=0; j<len1; j++) {
                       
                        for (var k = Math.max(0, j - maxDist); k < Math.min(len2, j + maxDist + 1); k++) {
                            if (s1[j] == s2[k] && hash2[k] == 0) {
                                hash1[j] = 1;
                                hash2[k] = 1;
                                matches++;
                                break;
                            }
                        }
                    }

                    var transpositions = 0;
                    var point = 0; 

                    for (let j=0; j<len1; j++) {
                        if (hash1[j]) {
                            while (hash2[point] == 0) {
                                point++;
                            }
                            if (s1[j] != s2[point++]) {
                                transpositions++;
                            }
                        }
                    }
                    transpositions /= 2;

                    

                    if (s1 == s2) {
                        similarityIndex = 1.0;
                    } else if (matches == 0) {
                        similarityIndex = 0.0;
                    } else {
                        similarityIndex = ((matches) / (len1) 
                                    + (matches) / (len2) 
                                    + (matches - transpositions) / (matches)) 
                                    / 3.0;
                    }

                    if (similarityIndex > 0.7) {
                        var prefix = 0;
                        for (let j=0; j<Math.min(s1.length, s2.length); j++) {
                            if (s1[j] == s2[j]) {
                                prefix++;
                            } else {
                                break;
                            } 
                        }
                        prefix = Math.min(4, prefix);
                        similarityIndex += 0.1 * prefix * (1 - similarityIndex);
                    }
                    this.stories[i].similarity = similarityIndex;
                }

                for(let i=0; i<this.stories.length; i++) {
                    if (this.stories[i].similarity > 0.65) {
                        this.displayStories.push(this.stories[i]);
                    }
                }

                this.displayStories.sort((a, b) => {
                    return b.similarity - a.similarity;
                });






            },
            
            filterTags: function() {
                this.reset();
                var tags = String(this.checkedTags).replace(/,/g, "+").replace(/ /g, "_");
                axios.get("/api/stories/tags/" + tags)
                .then(response=>{
                    this.displayStories = (response.data);
                    console.log(response.data);
                })
                .catch(response => {
                    this.returnData = [];
                    console.log(response.data);
                    console.log(response.response);
                })
            },

            reset: function() {
                this.displayStories = this.stories;
            }
        },
        mounted() {
            axios.get("/api/stories")
            .then(response=>{
                this.stories = Object.values(response.data);
                this.displayStories = this.stories;
            })
            .catch(response => {
                console.log(response.data);
            }),
            axios.get("/api/tags")
            .then(response=>{
                this.tags = Object.values(response.data);
            })
            .catch(response => {
                console.log(response.data);
            })
         
        }   
    });            
</script>
@endsection