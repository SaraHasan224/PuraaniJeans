<div class="card-body">
    <section>

        <div id="exampleAccordion" data-children=".item">
            <div class="item">
                <button type="button" aria-expanded="true" aria-controls="exampleAccordion1" data-toggle="collapse" href="#collapseExample" class="m-0 p-0 btn btn-link">
                    <h5 class="pb-3 card-title">Dashboard</h5>
                </button>
                <div data-parent="#exampleAccordion" id="collapseExample" class="collapse show">
                    <div class="row">
                        <div class="col-lg-6 col-xl-4">
                            <div class="card mb-3 widget-content bg-night-fade">
                                <div class="widget-content-wrapper text-white">
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Total Orders</div>
                                        <div class="widget-subheading">Number of created orders</div>
                                    </div>
                                    <div class="widget-content-right">
                                        <div class="widget-numbers text-white">
                                            <span>{{$data['dashboard']->order_count}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card mb-3 widget-content bg-arielle-smile">
                                <div class="widget-content-wrapper text-white">
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Products Viewed</div>
                                        <div class="widget-subheading">Total Number of Viewed Product Count</div>
                                    </div>
                                    <div class="widget-content-right">
                                        <div class="widget-numbers text-white">
                                            <span>{{$data['dashboard']->product_view_count}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card mb-3 widget-content bg-premium-dark">
                                <div class="widget-content-wrapper text-white">
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Products Sold</div>
                                        <div class="widget-subheading">Total number of placed orders</div>
                                    </div>
                                    <div class="widget-content-right">
                                        <div class="widget-numbers text-warning">
                                            <span>{{$data['dashboard']->product_sold_count}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-4">
                            <div class="card mb-3 widget-content bg-happy-green">
                                <div class="widget-content-wrapper text-white">
                                    <div class="widget-content-left">
                                        <div class="widget-heading">Followers</div>
                                        <div class="widget-subheading">People Interested In This Store</div>
                                    </div>
                                    <div class="widget-content-right">
                                        <div class="widget-numbers text-dark">
                                            <span>{{$data['dashboard']->follower_count}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="item">
                <button type="button" aria-expanded="true" aria-controls="exampleAccordion2" data-toggle="collapse" href="#collapseExample2" class="m-0 p-0 btn btn-link">
                    <h5 class="pb-3 card-title">Closet Information</h5>
                </button>
                <div data-parent="#exampleAccordion" id="collapseExample2" class="show">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <form id="closet_edit_form" class="newFormContainer" method="post" autocomplete="off">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 formFieldsWrap">

                                        <div class="form-group">
                                            <label>Closet Name</label>
                                            {{--<input--}}
                                                    {{--type="text"--}}
                                                    {{--name="closet_name"--}}
                                                    {{--maxlength="30"--}}
                                                    {{--disabled--}}
                                                    {{--placeholder="Closet Name"--}}
                                                    {{--class="form-control"--}}
                                                    {{--value="{{ !empty(old('first_name')) ? old('first_name') : (!empty($data['closet']->closet_name) ? $data['closet']->closet_name : '') }}"--}}
                                                    {{--required--}}
                                            {{-->--}}
                                            <div class="chat-wrapper">
                                                <div class="chat-box-wrapper">
                                                    <div>
                                                        <div class="chat-box">
                                                            {{$data['closet']->closet_name}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 formFieldsWrap">
                                            <div class="form-group">
                                            <label>Trending</label>
                                            @if($data['closet']->is_trending)
                                                    <a href="javascript:void(0);"
                                                       class="mb-2 mr-2 badge badge-alternate">Trending (Count: {{$data['closet']->trending_position}})</a>
                                            @else
                                                    <a href="javascript:void(0);" class="mb-2 mr-2 badge badge-secondary">Not
                                                        A Trending Closet</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 formFieldsWrap">
                                        <div class="form-group">
                                            <label>Logo *</label>
                                            <label class="cabinet center-block uploadLabelBlock">
                                                <figure>
                                                    <img
                                                            src="{{isset($data['closet']->logo)? $data['closet']->logo : ''}}"
                                                            class="croppie-image img-responsive img-thumbnail"
                                                            id="item-img-output"
                                                            width="250"
                                                            height="150"
                                                    />
                                                    {{--<figcaption><span>Upload </span><i class="icon-upload"></i></figcaption>--}}
                                                </figure>

                                                <input type="hidden" id="brandverseImage" name="brandverseImage"/>
                                                <input type="file"
                                                       id="image"
                                                       accept='image/*'
                                                       onclick="App.Helpers.clearInput(this)"
                                                       onchange="App.Helpers.uploadImage('form_product_update', 'image', '250', '100')"
                                                       class="item-img file center-block"
                                                       name="image"/>

                                                <input type="hidden" name="cropped_image"
                                                       id="cropped_image">
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 formFieldsWrap">
                                            <div class="form-group">
                                                <label>Banner *</label>
                                                <label class="cabinet center-block uploadLabelBlock">
                                                    <figure>
                                                        <img
                                                                src="{{isset($data['closet']->banner)? $data['closet']->banner : ''}}"
                                                                class="croppie-image img-responsive img-thumbnail"
                                                                id="item-img-output"
                                                                width="250"
                                                                height="150"
                                                        />
                                                        {{--<figcaption><span>Upload </span><i class="icon-upload"></i></figcaption>--}}
                                                    </figure>

                                                    <input type="hidden" id="brandverseImage" name="brandverseImage"/>
                                                    <input type="file"
                                                           id="image"
                                                           accept='image/*'
                                                           onclick="App.Helpers.clearInput(this)"
                                                           onchange="App.Helpers.uploadImage('form_product_update', 'image', '250', '100')"
                                                           class="item-img file center-block"
                                                           name="image"/>

                                                    <input type="hidden" name="cropped_image"
                                                           id="cropped_image">
                                                </label>
                                            </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 formFieldsWrap">
                                        <div class="form-group">
                                            <label>Description *</label>
                                            {{--<input--}}
                                                    {{--type="text"--}}
                                                    {{--name="username"--}}
                                                    {{--maxlength="100"--}}
                                                    {{--placeholder="username"--}}
                                                    {{--class="form-control"--}}
                                                    {{--value="{{ !empty(old('username')) ? old('username') : (!empty($customer) ? $customer->username : '') }}"--}}
                                                    {{--required--}}
                                            {{-->--}}

                                            <div class="chat-wrapper">
                                                <div class="chat-box-wrapper">
                                                    <div>
                                                        <div class="chat-box">
                                                            {{$data['closet']->about_closet}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-none">
                                    <div class="col-md-12 formFieldsWrap">
                                        <div class="form-group">
                                            <div class="insideButtons">
                                                <button id="edit-customer" type="button" disabled="true" class="btn btn-primary"><i
                                                            class="icon-check-thin newMargin"></i>Save
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<div class="d-block text-right card-footer">
    <a href="javascript:void(0);" class="btn-wide btn btn-success">Save</a>
</div>