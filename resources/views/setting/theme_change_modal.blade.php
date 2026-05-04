{{-- <div id="themeChangeModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="container" style="position: absolute;width: 1500px;transform: translate(-32%, -50%);background-color: #fff;">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 d-flex">
                    <div class="container-fluid card transparent-background">
                        <div class="row">
                            <div class="col-12 col-md-6 card p-3">
                                <div class="form-group flex justify-center">
                                    <a href="{{ route('themeChange') }}" data-turbo=false>
                                        <div class="" data-id="1">
                                            <img src="{{ asset('assets/theme1/images/theme1.png') }}" alt="Template"
                                                class="img-thumbnail p-0">
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 card p-3">
                                <div class="form-group background img-border flex justify-center md:mb-0 mb-7">
                                    <div class="border-4 border-primary bg-white opacity-50" data-id="2">
                                        <a href="javascript:void(0)">
                                            <img src="{{ asset('assets/theme1/images/theme2.png') }}" alt="Template"
                                                class="img-thumbnail p-0">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

{{-- <div id="themeChangeModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- Adjust the container to be responsive and centered -->
            <div class="container mx-auto p-6 bg-white max-w-6xl" style="transform: translate(-50%, -50%);">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Theme 1 card -->
                    <div class="p-3 flex justify-center">
                        <a href="{{ route('themeChange') }}" data-turbo="false">
                            <div class="card hover:shadow-lg transition-shadow duration-300" data-id="1">
                                <img src="{{ asset('assets/theme1/images/theme1.png') }}" alt="Template"
                                    class="img-thumbnail w-full h-auto object-cover">
                            </div>
                        </a>
                    </div>

                    <!-- Theme 2 card -->
                    <div class="p-3 flex justify-center">
                        <div class="card hover:shadow-lg transition-shadow duration-300" data-id="2">
                            <a href="javascript:void(0)">
                                <img src="{{ asset('assets/theme1/images/theme2.png') }}" alt="Template"
                                    class="img-thumbnail w-full h-auto object-cover border-4 border-primary">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}


<div id="themeChangeModal" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 1330px">
        <div class="modal-content">
            <div class="container mx-auto p-6 md:p-12 bg-white relative">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: flex">
                    <div class="flex justify-center">
                        <div class="card p-4 shadow-md">
                            <a href="{{ route('themeChange') }}" data-turbo="false">
                                <img src="{{ asset('assets/theme1/images/theme1.png') }}" alt="Template" class="img-thumbnail">
                            </a>
                        </div>
                    </div>
                    <div class="flex justify-center">
                        <div class="card p-4 shadow-md border-4 border-primary bg-white opacity-50">
                            <a href="javascript:void(0)">
                                <img src="{{ asset('assets/theme1/images/theme2.png') }}" alt="Template" class="img-thumbnail">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
