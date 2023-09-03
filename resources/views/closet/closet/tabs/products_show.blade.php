<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {{ $product->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Price:</strong>
            @if($product->discounted_price > 0)
            <span>
                PKR {{$product->discounted_price}}
                <del>
                    <span className="money">
                      PKR
                      {{$product->price}})}
                    </span>
                </del>
            </span>
            @else
                PKR {{ $product->price }}
            @endif
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Max Quantity:</strong>
            {{ $product->max_quantity }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Status:</strong>
            <span class="class-{{$productStyleStatus}}"> {{ $productStatus }}</span>
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Image:</strong>

            <img src="{{ $product->image }}" width="100" height="150"/>
            <br/>
            <button type="button" aria-expanded="false" aria-controls="exampleAccordion0" data-toggle="collapse" href="#collapseExample0" class="m-0 p-0 btn btn-link">
                <i>Click here to see more</i>
            </button>
            <div data-parent="#exampleAccordion" id="collapseExample0" class="hide collapse">
                <div class="row ml-2">
                    @foreach($product->images as $images)
                        <div class="col-xs-3 col-sm-3 col-md-3">
                            <div class="form-group">
                                <img src="{{ $images->url }}" width="100" height="150" class="mr-2"/>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Attributes:</strong>
            <button type="button" aria-expanded="false" aria-controls="exampleAccordion4" data-toggle="collapse" href="#collapseExample4" class="m-0 p-0 btn btn-link">
                <i>Click here to see more</i>
            </button>
            <div data-parent="#exampleAccordion" id="collapseExample4" class="hide collapse">
                <div class="row ml-2">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Category:</strong>
                            {{implode("| ", $product->item_information['category'])}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Sub Category:</strong>
                            {{implode("| ", $product->item_information['subCategory'])}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Brand:</strong>
                            {{!empty($product->item_information['brand']) ? $product->item_information['brand'] : ""}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Condition:</strong>
                            {{!empty($product->item_information['condition']) && is_array($product->item_information['condition']) ? implode("| ", $product->item_information['condition']) : ""}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Size:</strong>
                            {{!empty($product->item_information['size']) && is_array($product->item_information['size']) ? implode("| ", $product->item_information['size']) : ""}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Standard:</strong>
                            {{!empty($product->item_information['standard']) && is_array($product->item_information['standard']) ? implode("| ", $product->item_information['standard']) : ""}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Color:</strong>
                            {{!empty($product->item_information['color']) && is_array($product->item_information['color']) ? implode("| ", $product->item_information['color']) : ""}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <h5 class="m-0 p-0 card-title"><strong>Shipment:</strong></h5>
            <button type="button" aria-expanded="false" aria-controls="exampleAccordion1" data-toggle="collapse" href="#collapseExample1" class="m-0 p-0 btn btn-link">
                <i>Click here to expand</i>
            </button>
            <div data-parent="#exampleAccordion" id="collapseExample1" class="hide collapse">
                <div class="row ml-2">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            @php
                                $enableWWShipping = $product->enable_world_wide_shipping == 1;
                                $badgeStyle = $enableWWShipping ? "success" : "danger";
                                $badgeTitle = $enableWWShipping ? "Enabled" : "Disabled";
                                $country = !empty($rowdata['shipment_country_details']) ? $rowdata['shipment_country_details']['name'] : "";
                            @endphp
                            <strong>Shipment Country:</strong>
                            {{ $product->shipmentCountryDetails->name }}
                        </div>
                        @php
                            $enableWWShipping = $product->enable_world_wide_shipping == 1;
                            $badgeStyle = $enableWWShipping ? "success" : "danger";
                            $badgeTitle = $enableWWShipping ? "Enabled" : "Disabled";
                            $country = !empty($rowdata['shipment_country_details']) ? $rowdata['shipment_country_details']['name'] : "";
                        @endphp
                        <div class="form-group">
                            <b>World Wide Shipping: </b> <span class="class-{{$badgeStyle}}"> {{ $badgeTitle }}</span>
                        </div>
                        <div class="form-group">
                            @php
                                $enableFreeShipment = $product->free_shipment == 1;
                                $badgeStyle = $enableFreeShipment ? "success" : "danger";
                                $badgeTitle = $enableFreeShipment ? "Enabled" : "Disabled";
                            @endphp
                            <b>Enable Free Shipment: </b><span class="class-{{$badgeStyle}}"> {{ $badgeTitle }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <h5 class="m-0 p-0 card-title"><strong>Description:</strong></h5>
            <button type="button" aria-expanded="false" aria-controls="exampleAccordion2" data-toggle="collapse" href="#collapseExample2" class="m-0 p-0 btn btn-link">
                <i>Click here to expand</i>
            </button>
            <div data-parent="#exampleAccordion" id="collapseExample2" class="hide collapse">
                <div class="row ml-2">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        {!! $product->short_description !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <h5 class="m-0 p-0 card-title"><strong>Variants:</strong></h5>
            <button type="button" aria-expanded="false" aria-controls="exampleAccordion2" data-toggle="collapse" href="#collapseExample5" class="m-0 p-0 btn btn-link">
                <i>Click here to expand</i>
            </button>
            <div data-parent="#exampleAccordion" id="collapseExample5" class="hide collapse">
                @foreach($product->variants as $variant)
                    <div class="row ml-2">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {{ $variant->variant_name }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Price:</strong>
                                @if($variant->discounted_price > 0)
                                    <span>
                                    PKR {{$variant->discounted_price}}
                                                            <del>
                                        <span className="money">
                                          PKR
                                            {{$variant->price}}
                                        </span>
                                    </del>
                                </span>
                                @else
                                    PKR {{ $variant->price }}
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>Max Quantity:</strong>
                                {{ $variant->max_quantity }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <strong>SKU:</strong>
                                {{ $variant->sku }}
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            <div class="form-group">
                                <h5 class="m-0 p-0 card-title"><strong>Description:</strong></h5>
                                {!! $variant->variant_short_description !!}
                            </div>
                        </div>
                    </div>
                    <hr/>
                @endforeach
            </div>
        </div>
    </div>
</div>