<style>
    .no-gap {
        gap: 0 !important;
    }
</style>
<div>
    <x-filament::input.wrapper id="sidebar-search-wrapper">
        <x-filament::input type="search" placeholder="{{ __('messages.search') . '...' }}" id="sidebar-search"
            onkeyup="attachSearchEvent()" />
    </x-filament::input.wrapper>
    <span id="no-results" style="display: none; margin: 15px;" class="text-yellow">
        {{ __('messages.no_matching_records_found') }}
    </span>

    <script>
        function attachSearchEvent() {
            const searchInput = document.getElementById('sidebar-search');
            const noResultsDiv = document.getElementById('no-results');
            const menuItems = document.querySelectorAll('.fi-sidebar-item-button');
            const sidebarGroups = document.querySelectorAll('.fi-sidebar-group-items');

            searchInput.addEventListener('input', function(event) {
                const query = event.target.value.toLowerCase();
                let found = false;

                menuItems.forEach(function(item) {
                    if (item.textContent.toLowerCase().includes(query)) {
                        item.style.display = '';
                        found = true;
                    } else {
                        item.style.display = 'none';
                    }
                });

                sidebarGroups.forEach(function(group) {
                    const groupItemButtons = group.querySelectorAll('.fi-sidebar-group-item');
                    let groupHasVisibleItems = false;

                    // Determine if the group has visible items
                    groupItemButtons.forEach(function(item) {
                        if (item.style.display !== 'none') {
                            groupHasVisibleItems = true;
                        }
                    });

                    // Show or hide the group based on the visibility of its items
                    if (groupHasVisibleItems) {
                        group.classList.remove('no-gap');
                    } else {
                        group.classList.add('no-gap');
                    }
                });

                if (!found) {
                    noResultsDiv.style.display = 'block';
                } else {
                    noResultsDiv.style.display = 'none';
                }

                // Reset search if input is empty
                if (query === '') {
                    menuItems.forEach(function(item) {
                        item.style.display = '';
                    });
                    sidebarGroups.forEach(function(group) {
                        group.classList.remove('no-gap');
                    });
                    noResultsDiv.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            attachSearchEvent();
        });
    </script>
</div>
<script>
    var isOpen = localStorage.getItem('isOpen');

    if (isOpen !== null && isOpen !== '') {
        var isOpenBoolean = JSON.parse(isOpen);

        if (isOpenBoolean) {
            document.getElementById("sidebar-search-wrapper").classList.remove("hidden");
        } else {
            document.getElementById("sidebar-search-wrapper").classList.add("hidden");
        }
    }

    document.addEventListener("click", (event) => {
        const target = event.target.closest("[x-on\\:click]");
        if (event.target.tagName == "BUTTON" || event.target.tagName == "svg") {
            if (target && target.getAttribute("x-on:click").includes("store.sidebar.close()")) {
                document.getElementById("sidebar-search-wrapper").classList.add("hidden");
            } else {
                document.getElementById("sidebar-search-wrapper").classList.remove("hidden");
            }
        }
    });
</script>
