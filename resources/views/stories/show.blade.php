@extends('layouts.app')

@section('title', 'Stories')

@section('content')
<div id="root">
    <h3>@{{ story.title }} </h3>
    <h4>@{{ story.description}}</h4>
    <ul> 
        <li>Played: @{{ story.times_played }} times</li>
        <li>Average Rating: @{{ averageRating }}</li>
    </ul>
    <h3>Reviews</h3>
    <ul v-for="review in reviews" :key="review.review">
        <li>@{{ review.review }}</li>
        <li>@{{ review.rating }}</li>
    </ul>
       
</div>
<script>
    var app = new Vue({
        el: "#root",
        data: {
            story: [],
            reviews: [],
            averageRating: 0,
        },
        methods: {
        },
        mounted() {
            axios.get("/api/stories/" + {{ $story->id }})
            .then(response=>{
                this.story = response.data;
            })
            .catch(response => {
                console.log(response.data);
            })

            axios.get("/api/stories/" + {{ $story->id }} + "/reviews")
            .then(response=>{
                this.reviews = Object.values(response.data);
                allReviews = 0;
                for(let i=0; i<this.reviews.length; i++) {
                    allReviews += this.reviews[i].rating;
                }
                

                this.averageRating = (allReviews / this.reviews.length, 2).toPrecision(2);
            })
            .catch(response => {
                console.log(response.data);
            })
        }
    });            
</script>
@endsection