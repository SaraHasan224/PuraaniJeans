<!-- Static Filter Wrap Start -->
<div class="main-card mb-3 card d-none">
    <div class="card-body">
        <form id="search-form" class="form-inline">
            <div class="ColSearchWrap">
                <div class="position-relative form-group">
                    <label for="exampleCustomSelect" class="">Custom Select</label>
                    <select type="select" id="exampleCustomMutlipleSelect js-select2" name="customSelect" class="customSelect" name="allCols[]" multiple="multiple">
                        <option value="">Select</option>
                        <option value="all" data-badge="">All Columns</option>
                        <option value="user_id" data-badge="">User ID</option>
                        <option value="email" data-badge="">User Email</option>
                        <option value="filter_phone" data-badge="">Mobile Number</option>
                    </select>
                    <input type="hidden" id="user_id" value="">
                    <input type="hidden" id="email" value="">
                    <input type="hidden" id="filter_phone" value="">
                </div>
                <div class="form-inline headerSearchBar">
                    <i class="icon-search searchIcon"></i>
                    <input class="form-control" onkeyup="App.Helpers.filterColumnsSearch();"
                           onblur="App.Helpers.filterColumnsSearch();" id="filterColumnsSearch" type="search"
                           placeholder="Search">
                </div>
            </div>
            <input type="hidden" id="currentUrl" value="{{Route::currentRouteName()}}">
        </form>
    </div>
</div>
<!-- Static Filter Wrap End -->