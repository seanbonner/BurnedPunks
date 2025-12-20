jQuery(document).ready(function ($) {

    "use strict";

    const vimeoDivs = document.getElementsByClassName("minimalio-vimeo");

    const vimeoDivArray = [...vimeoDivs]



    if (vimeoDivs != null) {

        vimeoDivArray.forEach(vimeoDiv => {

            const overlayDivElement = vimeoDiv.getElementsByClassName("vimeo-iframe-overlay");

            if (overlayDivElement.length != 0) {

                const buttonElement = document.createElement("button");

                overlayDivElement[0].appendChild(buttonElement);

                buttonElement.style.background = overlayDivElement[0].dataset.controlsColor

                const iframe = vimeoDiv.querySelector("iframe");

                // Check if Vimeo API is available
                if (typeof Vimeo === 'undefined') {
                    console.warn('Vimeo API not loaded for iframe script');
                    return;
                }
                
                const player = new Vimeo.Player(iframe);

                buttonElement.addEventListener("click", function () {

                    player.play();

                    overlayDivElement[0].style.opacity = "0"

                    function hideoverlay() {

                        overlayDivElement[0].style.display = "none"

                    }

                    setTimeout(hideoverlay, 2000);

                });

            }

        });

    }

});
jQuery(document).ready(function ($) {
    "use strict";

    // Prevent multiple initialization
    if (window.vimeoBannerInitialized) {
        return;
    }
    window.vimeoBannerInitialized = true;

    function initializeVimeoPlayers() {
        // const vimeoDiv = document.getElementById("vimeo-banner");
        const vimeoDivs = document.getElementsByClassName("vimeo-banner");
        const vimeoDivArray = [...vimeoDivs]

        if (vimeoDivs != null) {
            vimeoDivArray.forEach((vimeoDiv, index) => {

                // Add small delay between player initializations to prevent conflicts
                setTimeout(function () {
                    const vimeoElement = vimeoDiv.parentNode.parentNode
                    
                    function changeHeight() {
                        requestAnimationFrame(() => {
                            const height = vimeoElement.dataset.height;
                            const heightMobile = vimeoElement.dataset.heightmobile;
                            const heightTablet = vimeoElement.dataset.heighttablet;
                            const heightDesktop = vimeoElement.dataset.heightdesktop;
                            const videoRatio = vimeoElement.dataset.ratio;
                            const frameElement = vimeoElement.querySelector(".minimalio-vimeo-banner__frame");
                            
                            // Ensure frameElement exists and has dimensions
                            if (!frameElement || !frameElement.offsetWidth || !frameElement.offsetHeight) {
                                // Retry after a short delay if dimensions aren't available
                                setTimeout(changeHeight, 100);
                                return;
                            }
                            
                            const ratio = frameElement.offsetWidth / frameElement.offsetHeight;

                            if (height == "custom") {
                                if (window.innerWidth > 992) {
                                    vimeoElement.children[0].style.height = heightDesktop + "vh";
                                } else if (window.innerWidth < 992 && window.innerWidth > 767) {
                                    vimeoElement.children[0].style.height = heightTablet + "vh";
                                } else {
                                    vimeoElement.children[0].style.height = heightMobile + "vh";
                                }
                            }

                            if (videoRatio === "16-9") {
                                if (ratio > 1.77) {
                                    vimeoElement.children[0].dataset.orientation = "horizontal";
                                } else {
                                    vimeoElement.children[0].dataset.orientation = "vertical";
                                }
                            } else if (videoRatio === "4-3") {
                                if (ratio > 1.33) {
                                    vimeoElement.children[0].dataset.orientation = "horizontal";
                                } else {
                                    vimeoElement.children[0].dataset.orientation = "vertical";
                                }
                            } else {
                                if (ratio > 2.39) {
                                    vimeoElement.children[0].dataset.orientation = "horizontal";
                                } else {
                                    vimeoElement.children[0].dataset.orientation = "vertical";
                                }
                            }
                        });
                    }

                    // Enhanced mobile orientation handling
                    function createOrientationHandler() {
                        let orientationTimeout;
                        let resizeTimeout;
                        let lastOrientation = null;
                        
                        // Detect current orientation using matchMedia
                        const portraitQuery = window.matchMedia("(orientation: portrait)");
                        const landscapeQuery = window.matchMedia("(orientation: landscape)");
                        
                        function handleOrientationChange() {
                            // Clear any pending timeouts
                            if (orientationTimeout) clearTimeout(orientationTimeout);
                            if (resizeTimeout) clearTimeout(resizeTimeout);
                            
                            const currentOrientation = portraitQuery.matches ? 'portrait' : 'landscape';
                            
                            // Only proceed if orientation actually changed
                            if (currentOrientation !== lastOrientation) {
                                lastOrientation = currentOrientation;
                                
                                // Multiple timeout intervals to handle different devices
                                changeHeight();                    // Immediate call
                                setTimeout(changeHeight, 100);     // Short delay for Android
                                setTimeout(changeHeight, 500);     // Medium delay for iOS
                                orientationTimeout = setTimeout(changeHeight, 1000); // Long delay for problematic iOS Safari
                            }
                        }
                        
                        function handleResize() {
                            // Debounce resize events to prevent excessive calls
                            if (resizeTimeout) clearTimeout(resizeTimeout);
                            resizeTimeout = setTimeout(changeHeight, 50);
                        }
                        
                        // Modern screen orientation API (preferred)
                        if (screen.orientation && screen.orientation.addEventListener) {
                            screen.orientation.addEventListener('change', handleOrientationChange);
                        }
                        
                        // matchMedia listeners for orientation changes (reliable fallback)
                        if (portraitQuery.addEventListener) {
                            portraitQuery.addEventListener('change', handleOrientationChange);
                            landscapeQuery.addEventListener('change', handleOrientationChange);
                        } else {
                            // Fallback for older browsers
                            portraitQuery.addListener(handleOrientationChange);
                            landscapeQuery.addListener(handleOrientationChange);
                        }
                        
                        // Traditional resize event (fallback)
                        window.addEventListener('resize', handleResize);
                        
                        // Deprecated orientationchange event (for older devices)
                        if ('orientationchange' in window) {
                            window.addEventListener('orientationchange', handleOrientationChange);
                        }
                        
                        // Visual Viewport API for iOS 13+ (handles address bar changes)
                        if (window.visualViewport) {
                            window.visualViewport.addEventListener('resize', handleResize);
                        }
                        
                        // Initial orientation setup
                        lastOrientation = portraitQuery.matches ? 'portrait' : 'landscape';
                        
                        return {
                            destroy: function() {
                                if (screen.orientation && screen.orientation.removeEventListener) {
                                    screen.orientation.removeEventListener('change', handleOrientationChange);
                                }
                                if (portraitQuery.removeEventListener) {
                                    portraitQuery.removeEventListener('change', handleOrientationChange);
                                    landscapeQuery.removeEventListener('change', handleOrientationChange);
                                } else {
                                    portraitQuery.removeListener(handleOrientationChange);
                                    landscapeQuery.removeListener(handleOrientationChange);
                                }
                                window.removeEventListener('resize', handleResize);
                                if ('orientationchange' in window) {
                                    window.removeEventListener('orientationchange', handleOrientationChange);
                                }
                                if (window.visualViewport) {
                                    window.visualViewport.removeEventListener('resize', handleResize);
                                }
                                if (orientationTimeout) clearTimeout(orientationTimeout);
                                if (resizeTimeout) clearTimeout(resizeTimeout);
                            }
                        };
                    }
                    
                    // Create orientation handler for this vimeo element
                    const orientationHandler = createOrientationHandler();
                    
                    // Store reference for cleanup if needed
                    vimeoElement._orientationHandler = orientationHandler;
                    
                    // Initial call
                    changeHeight();

                    function player() {
                        // Check if Vimeo API is available
                        if (typeof Vimeo === 'undefined') {
                            return;
                        }

                        // Skip if player already initialized
                        if (vimeoDiv.hasAttribute('data-player-initialized')) {
                            return;
                        }

                        const vimeoId = vimeoDiv.dataset.vimeo;

                        // Generate unique ID for this player if it doesn't have one
                        if (!vimeoDiv.id) {
                            vimeoDiv.id = 'vimeo-player-' + Math.random().toString(36).substr(2, 9);
                        }

                        const options = {
                            url: vimeoId,
                            loop: true,
                            autoplay: true,
                            autopause: false,
                            controls: false,
                            muted: true,
                            title: false,
                            portrait: false,
                            transparent: true,
                            // Note: Feature Policy warnings from Vimeo iframe are handled internally by Vimeo API
                            // and cannot be controlled from this implementation
                        };

                        try {
                            const player = new Vimeo.Player(vimeoDiv, options);

                            // Mark as initialized
                            vimeoDiv.setAttribute('data-player-initialized', 'true');

                            // controls

                            const controlsWrapper = vimeoElement.getElementsByClassName(
                                "vimeo-background-controls"
                            );

                            // Check if controls already exist to prevent duplicates
                            if (controlsWrapper[0].children.length === 0) {
                                const controlPlay = document.createElement("button");
                                controlPlay.className = "play-toggle";
                                controlPlay.style.background = controlsWrapper[0].dataset.color;
                                const controlMute = document.createElement("button");
                                controlMute.className = "mute-toggle";
                                controlMute.style.background = controlsWrapper[0].dataset.color;

                                controlsWrapper[0].appendChild(controlPlay);
                                controlsWrapper[0].appendChild(controlMute);
                            }

                            // Get the actual control elements (whether just created or existing)
                            const controlPlay = controlsWrapper[0].querySelector('.play-toggle');
                            const controlMute = controlsWrapper[0].querySelector('.mute-toggle');


                            // Only add event listeners if they haven't been added yet
                            if (!controlPlay.hasAttribute('data-initialized')) {
                                controlPlay.setAttribute('data-initialized', 'true');

                                controlPlay.addEventListener("click", function () {
                                    player.getPaused().then(function (paused) {
                                        if (paused) {
                                            player.play();
                                            controlPlay.classList.remove("paused");
                                        } else {
                                            player.pause();
                                            controlPlay.classList.add("paused");
                                        }
                                    });
                                });
                            }

                            if (!controlMute.hasAttribute('data-initialized')) {
                                controlMute.setAttribute('data-initialized', 'true');

                                player
                                    .getVolume()
                                    .then(function (volume) {
                                        let currentVolume = "0";
                                        controlMute.addEventListener("click", function () {
                                            if (currentVolume === "0") {
                                                player.setVolume(1);
                                                currentVolume = "1";
                                                controlMute.classList.toggle("muted");
                                            } else if (currentVolume === "1") {
                                                player.setVolume(0);
                                                currentVolume = "0";
                                                controlMute.classList.toggle("muted");
                                            }
                                        });
                                    })
                                    .catch(function (error) {
                                        // an error occurred
                                    });
                            }
                        } catch (error) {
                            // Error creating Vimeo player
                        }
                    }
                    player();
                }, index * 200); // 200ms delay between each player
            })
        }
    }

    // Load Vimeo API conditionally when this script is enqueued
    function loadVimeoAPI() {
        return new Promise((resolve, reject) => {
            if (typeof Vimeo !== 'undefined') {
                resolve();
                return;
            }
            
            if (window.vimeoAPILoading) {
                // Wait for existing load to complete
                const checkLoaded = () => {
                    if (typeof Vimeo !== 'undefined') {
                        resolve();
                    } else {
                        setTimeout(checkLoaded, 100);
                    }
                };
                checkLoaded();
                return;
            }
            
            window.vimeoAPILoading = true;
            const script = document.createElement('script');
            script.src = 'https://player.vimeo.com/api/player.js';
            script.onload = () => {
                window.vimeoAPILoading = false;
                resolve();
            };
            script.onerror = () => {
                window.vimeoAPILoading = false;
                reject(new Error('Failed to load Vimeo API'));
            };
            document.head.appendChild(script);
        });
    }

    // Load API and initialize players
    loadVimeoAPI().then(() => {
        initializeVimeoPlayers();
    }).catch((error) => {
        console.error('Could not load Vimeo API:', error);
    });
});
