<!-- Static Filter Wrap Start -->
<div class="main-card mb-3 card">
    <div class="card-body">
        <form method="POST" id="search-form" class="filterForm form-inline" role="form">
            @csrf

            <div class="form-group">
                <input
                        type="text"
                        name="name"
                        id="name"
                        placeholder="User Name"
                        class="form-control mr-3"
                />
            </div>

            <div class="form-group">
                <input
                        type="email"
                        name="email"
                        id="email"
                        placeholder="User Email"
                        class="form-control mr-3"
                />
            </div>

            <div class="form-group">
                <input
                        type="tel"
                        name="phone"
                        id="phone"
                        placeholder="User Phone Number"
                        class="form-control mr-3"
                />
            </div>

            <div class="form-group filterButtons">
                <button type="submit" class="btn btn-primary filter-col mr-2">Search</button>
                <input type="button" onclick="App.Users.removeFilters();"
                       class="btn btn-primary filter-col mr-2" value="Remove Filters"/>
            </div>

            <div class="form-group" style="padding-left: 5px;">
                <button onclick="App.Helpers.refreshDataTable();" class="btn btn-info" type="button">Refresh</button>
            </div>
        </form>
    </div>
</div>
<!-- Static Filter Wrap End -->