<div class="modal-backdrop bg-black/25 flex justify-center items-center opacity-1 z-20 fixed inset-0 hidden">
    <div id="myModal" class="modal invisible fixed bg-transparent z-50">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="container">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="card p-3">
                            <div class="flex justify-center md:mb-0 mb-7">
                                <div class="border-4 border-primary bg-white" data-id="1">
                                    <a href="javascript:void(0)" data-turbo="false">
                                        <img src="{{ asset('assets/theme1/images/theme1.png') }}" alt="Template"
                                            class=" p-0 opacity-50">
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card p-3">
                            <div class="flex justify-center">
                                <a href="{{ route('themeChange') }}" data-turbo="false">
                                    <div class="" data-id="2">
                                        <img src="{{ asset('assets/theme1/images/theme2.png') }}" alt="Template"
                                            class="p-0 hover:border-4 hover:border-green hover:duration-100">
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
