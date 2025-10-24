                    <!-- Page Content Ends Here -->
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile Dock Navigation -->
    <div class="dock dock-sm fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50 lg:hidden">
        <a href="index.php" class="dock-item <?php echo $currentPage == 'index.php' ? 'dock-active' : ''; ?>">
            <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                    <polyline points="1 11 12 2 23 11" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline>
                    <path d="m5,13v7c0,1.105.895,2,2,2h10c1.105,0,2-.895,2-2v-7" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path>
                    <line x1="12" y1="22" x2="12" y2="18" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></line>
                </g>
            </svg>
        </a>

        <a href="expenses.php" class="dock-item <?php echo $currentPage == 'expenses.php' ? 'dock-active' : ''; ?>">
            <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                    <polyline points="3 14 9 14 9 17 15 17 15 14 21 14" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="2"></polyline>
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></rect>
                </g>
            </svg>
        </a>

        <a href="categories.php" class="dock-item <?php echo $currentPage == 'categories.php' ? 'dock-active' : ''; ?>">
            <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                    <path d="m7,7h.01M7,3h5c.512,0,1.024.195,1.414.586l7,7c0,0,0,1.414,0,2.828l-7,7c0,0-.707.707-1.414.707l-7-7c0,0-.586-.586-.586-1.414V7c0-2.209,1.791-4,4-4Z" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path>
                </g>
            </svg>
        </a>

        <a href="report.php" class="dock-item <?php echo $currentPage == 'report.php' ? 'dock-active' : ''; ?>">
            <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                    <path d="m9,19v-6c0-1.105-.895-2-2-2H5c-1.105,0-2,.895-2,2v6c0,1.105.895,2,2,2h2c1.105,0,2-.895,2-2Zm0,0V9c0-1.105.895-2,2-2h2c1.105,0,2,.895,2,2v10m-6,0c0,1.105.895,2,2,2h2c1.105,0,2-.895,2-2m0,0V5c0-1.105.895-2,2-2h2c1.105,0,2,.895,2,2v14c0,1.105-.895,2-2,2h-2c-1.105,0-2-.895-2-2Z" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path>
                </g>
            </svg>
        </a>

        <a href="backup.php" class="dock-item <?php echo $currentPage == 'backup.php' ? 'dock-active' : ''; ?>">
            <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                    <path d="m5,12h14M5,12c-1.105,0-2-1.791-2-4V6c0-1.105.895-2,2-2h14c1.105,0,2,.895,2,2v2c0,2.209-1.791,4-4,4M5,12c-1.105,0-2,1.791-2,4v4c0,1.105.895,2,2,2h14c1.105,0,2-.895,2-2v-4c0-2.209-1.791-4-4-4m-2-4h.01M17,16h.01" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path>
                </g>
            </svg>
        </a>

        <a href="settings.php" class="dock-item <?php echo $currentPage == 'settings.php' ? 'dock-active' : ''; ?>">
            <svg class="size-[1.2em]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                <g fill="currentColor" stroke-linejoin="miter" stroke-linecap="butt">
                    <circle cx="12" cy="12" r="3" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></circle>
                    <path d="m22,13.25v-2.5l-2.318-.966c-.167-.581-.395-1.135-.682-1.654l.954-2.318-1.768-1.768-2.318.954c-.518-.287-1.073-.515-1.654-.682l-.966-2.318h-2.5l-.966,2.318c-.581.167-1.135.395-1.654.682l-2.318-.954-1.768,1.768.954,2.318c-.287.518-.515,1.073-.682,1.654l-2.318.966v2.5l2.318.966c.167.581.395,1.135.682,1.654l-.954,2.318,1.768,1.768,2.318-.954c.518.287,1.073.515,1.654.682l.966,2.318h2.5l.966-2.318c.581-.167,1.135-.395,1.654-.682l2.318.954,1.768-1.768-.954-2.318c.287-.518.515-1.073.682-1.654l2.318-.966Z" fill="none" stroke="currentColor" stroke-linecap="square" stroke-miterlimit="10" stroke-width="2"></path>
                </g>
            </svg>
        </a>
    </div>

    <!-- Theme Toggle Script -->
    <script>
        // No sidebar functionality needed anymore
    </script>
</body>
</html>
