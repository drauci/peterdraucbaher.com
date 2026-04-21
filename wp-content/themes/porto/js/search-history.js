/**
 * Porto Search History Library
 * 
 * @package    Porto
 * @author     P-THEMES
 * @since      3.8.0
 */

'use strict';

window.theme = window.theme || {};

(function ($) {
    theme.initSearchHistory = function () {
        var localStorageName = 'porto_search_history',
            itemsLimit = 10;

        if (js_porto_vars.is_multisite) {
            localStorageName += '_' + js_porto_vars.current_blog_id;
        }
        var init = function () {
            var historyLists = document.querySelectorAll('.search-history-list');

            historyLists.forEach(function (list) {
                var form = list.closest('.searchform-popup').querySelector('.searchform'),
                    input = form.querySelector('.text input');

                $(form).on('submit', saveSearchHistoryEvent);

                $( input ).on('porto_sh_before_open', searchHistoryEvent);

                list.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                });
            });
        }

        var saveSearchHistoryEvent = function (e) {
            var searchInput = e.target.querySelector('.text input');

            addToSearchHistory(searchInput.value);
        }

        var updateHistoryEvent = function (e) {
            var value = this.textContent;

            addToSearchHistory(value);
        }

        var searchHistoryEvent = function (e) {
            var input = this;
            var wrapper = input.closest('.searchform-popup');
            var resultsNode = wrapper.querySelector('.search-history-list');

            if (!resultsNode) {
                return;
            }

            renderSearchHistory(wrapper, resultsNode);
        }

        var renderSearchHistory = function (wrapper, resultsNode) {
            var searchHistory = getSearchHistory().reverse();

            resultsNode.innerHTML = '';

            if (searchHistory.length > 0) {
                wrapper.querySelector('.search-lists').classList.add('show');

                var titleItem = createHistoryTitle();
                var ul = document.createElement('ul');

                resultsNode.classList.remove('empty');
                resultsNode.appendChild(titleItem);
                resultsNode.appendChild(ul);

                searchHistory.forEach(function (searchQuery) {
                    searchQuery = searchQuery.replaceAll('%20', ' ');

                    var url = new URL(js_porto_vars.home_url);

                    url.searchParams.set('s', searchQuery);

                    var postType = wrapper.querySelector('[name="post_type"]') ? wrapper.querySelector('[name="post_type"]').value : '';

                    if (postType) {
                        url.searchParams.set('post_type', postType);
                    }

                    var itemNode = createHistoryItem(searchQuery, url.href);

                    resultsNode.querySelector('ul').appendChild(itemNode);
                });
            }
        }

        var createHistoryTitle = function () {
            var title = document.createElement('span');
            var clearBtn = document.createElement('span');
            var wrapper = document.createElement('div');

            title.textContent = js_porto_vars.texts.search_history_title;
            title.classList.add('title');
            wrapper.appendChild(title);

            clearBtn.classList.add('sh-clear');
            clearBtn.textContent = js_porto_vars.texts.search_history_clear_all;
            clearBtn.addEventListener('click', clearAllEvent);
            wrapper.appendChild(clearBtn);

            wrapper.classList.add('sh-header');

            return wrapper;
        }

        var createHistoryItem = function (text, href) {
            var clearBtn = document.createElement('span');
            var linkNode = document.createElement('a');
            var item = document.createElement('li');

            clearBtn.classList.add('sh-clear');
            clearBtn.addEventListener('click', clearItemEvent);
            clearBtn.innerHTML = '<svg width=".8125rem" height=".8125rem" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g id="cross"><line stroke-width="2px" x1="7" x2="25" y1="7" y2="25"></line><line stroke-width="2px" x1="7" x2="25" y1="25" y2="7"></line></g></svg>';
            linkNode.textContent = text;
            linkNode.setAttribute('href', href);
            linkNode.classList.add('sh-link');
            linkNode.addEventListener('click', updateHistoryEvent);

            item.appendChild(linkNode);
            item.appendChild(clearBtn);

            return item;
        }

        var clearAllEvent = function (e) {
            e.preventDefault();
            e.stopPropagation();

            localStorage.removeItem(localStorageName)

            this.closest('.search-history-list').classList.add('empty');
            this.closest('.search-history-list').innerHTML = '';
        }

        var clearItemEvent = function (e) {
            e.preventDefault();
            e.stopPropagation();

            var searchValue = this.previousSibling.textContent.replaceAll('%20', ' ');
            var searchHistory = getSearchHistory();

            var newSearchHistory = searchHistory.filter(function (item) {
                return item !== searchValue;
            });

            localStorage.setItem(localStorageName, JSON.stringify(newSearchHistory));

            var listNode = this.closest('ul');

            this.closest('li').remove();

            if (0 === listNode.childElementCount) {
                listNode.closest('.search-history-list').classList.add('empty');
                listNode.closest('.search-history-list').innerHTML = '';
            }
        }

        var getSearchHistory = function () {
            var data = localStorage.getItem(localStorageName) ? JSON.parse(localStorage.getItem(localStorageName)) : [];

            data = data.filter(function (item) {
                return item !== "" && item !== null && item !== undefined;
            });

            // Limit to show items.
            if (itemsLimit > 0 && data.length > itemsLimit) {
                data = data.slice(-itemsLimit);
            }

            data = data.map(function (item) {
                return item.replaceAll('%20', ' ');
            });

            return data;
        }

        var addToSearchHistory = function (value) {
            var searchHistory = getSearchHistory();

            // Remove duplicate entries (case-insensitive) before adding the new search term.
            searchHistory = searchHistory.filter(function (item) {
                return item.toLowerCase().trim() !== value.toLowerCase().trim();
            });

            value = value.replace('%20', ' ');

            searchHistory.push(value.trim());

            localStorage.setItem(localStorageName, JSON.stringify(searchHistory));
        }

        init();
    }

    $( document ).ready( function () {
        if ( $( '.search-history-list' ).length ) {
            theme.initSearchHistory();
        }
    })
})(jQuery);