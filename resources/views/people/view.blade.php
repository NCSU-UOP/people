@extends('layouts.app')

@section('content')
<div class="container" data-aos="fade-up">
    <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-9">
        <form class="card card-sm text-center">
        <div class="card-body row no-gutters align-items-center">
            <div class="col">
                <div class="input-group">
                        <span class="input-group-text text-primary border-primary" id="basic-addon1">
                            <i class="bi bi-search"></i>
                        </span>
                        <input class="form-control border-primary" type="search" placeholder="Search people" id="search-input" autofocus="">
                        <div class="form-floating">
                        <select class="form-select" id="floatingUserSelect" aria-label="Floating label select example">
                            <option value="1">Students</option>
                            <option value="2">Academic Staff</option>
                            <option value="3">Non-Academic Staff</option>
                        </select>
                        <label for="floatingSelect">User Type</label>
                        </div>
                        <div class="form-floating">
                            <select class="form-select" id="floatingSelect" aria-label="Floating label">
                                <option selected>Default</option>
                                <option value="1">by Name</option>
                                <option value="2">by RegNo</option>
                            </select>
                        <label for="floatingSelect">Advanced</label>
                        </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    </div>
    <div class="row">
        <div class="list-group col-lg-10 col p-2 mx-auto">
            <div id="results-container"></div>
        </div>
    </div>
</div>

<div class="container pt-4" style="display: flex; justify-content:center;">
    <div class="row" style="justify-content:center;">
        <div class="card bg-light m-3" data-aos="zoom-in" data-aos-delay=100 style="max-width: 18rem;">
            <div class="card-header text-center">Academic Staff</div>
            <img class="card-img-top" src="/img/staff.jpg" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">Academic Staff Catalogue</h5>
                <p class="card-text">Click Explore button to view Academic staff details</p>
            </div>
            <a href="people/academic" type="button" class="btn btn-outline-secondary btn-block mb-3">Explore</a>
        </div>
        <div class="card bg-light m-3" data-aos="zoom-in" data-aos-delay=200 style="max-width: 18rem;">
            <div class="card-header text-center">Students</div>
            <img class="card-img-top" src="/img/student.jpg" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">Student Catalogue</h5>
                <p class="card-text">Click Explore button to view Students details</p>
            </div>
            <a href="people/student" type="button" class="btn btn-outline-secondary btn-block mb-3">Explore</a>
        </div>
        <div class="card bg-light m-3" data-aos="zoom-in" data-aos-delay=300 style="max-width: 18rem;">
            <div class="card-header text-center">Non-Academic Staff</div>
            <img class="card-img-top" src="/img/non-academic.jpg" alt="Card image cap">
            <div class="card-body">
                <h5 class="card-title">Non-academic Catalogue</h5>
                <p class="card-text">Click Explore button to view Non-academic staff details</p>
            </div>
            <a href="people/nonacademic" type="button" class="btn btn-outline-secondary btn-block mb-3">Explore</a>
        </div>
    </div>
    </div>
</div>
@endsection

@section('search-script')
<script>
  $(document).ready(function() {
    $('#search-input').on('input', function() {
            let query = $(this).val();
            let type = $('#floatingSelect').val();
            let user = $('#floatingUserSelect').val();
            if(query) 
            {
                $.ajax({
                    url: '/search',
                    type: "GET",
                    data : {"q":query, "type":type, "user":user},
                    dataType: "json",
                    success:function(data) {
                        console.log(data.length);
                        if(data)
                        {
                            if(data.length>0){
                            $('#results-container').empty();
                            $('#results-container').focus;
                            $.each(data, function(key, value){$('#results-container').append('<a href="/student/'+ value.id +'"class="list-group-item list-group-item-action">' + value.fullname + ' ('+value.regNo+')' + '</a>');});
                            }
                            else if(user != 1){
                                $('#results-container').empty();
                                $('#results-container').focus;
                                $('#results-container').append('<a href="/comingsoon" class="list-group-item list-group-item-action">Currently support Student Search only</a>');
                            }
                            else{
                                $('#results-container').empty();
                                $('#results-container').focus;
                                $('#results-container').append('<a href="#" class="list-group-item list-group-item-action">No results found</a>');
                            }
                        }
                        else
                        {
                            $('#results-container').empty();
                        }
                        }
                });
            }
            else
            {
                $('#results-container').empty();
            }
        });
    });
</script>
@endsection

