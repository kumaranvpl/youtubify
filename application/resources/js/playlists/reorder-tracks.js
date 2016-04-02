angular.module('app').directive('reorderTracks', function($http) {
    return {
        restrict: 'A',
        link: function ($scope, el) {
            $scope.$watch('sortedTracks', function(newTracks, oldTracks) {
                if (newTracks && newTracks !== oldTracks) {
                    initDraggables(el, $scope);
                }
            });
        }
    };

    function initDraggables(el, $scope) {
        rows = el[0].querySelectorAll('.flex-table-row');
        scope = $scope;

        angular.forEach(rows, function(row) {
            row.removeEventListener('dragstart', handleDragStart, false);
            row.addEventListener('dragstart', handleDragStart, false);
            row.removeEventListener('dragenter', handleDragEnter, false);
            row.addEventListener('dragenter', handleDragEnter, false);
            row.removeEventListener('dragover', handleDragOver, false);
            row.addEventListener('dragover', handleDragOver, false);
            row.removeEventListener('dragleave', handleDragLeave, false);
            row.addEventListener('dragleave', handleDragLeave, false);
            row.removeEventListener('drop', handleDrop, false);
            row.addEventListener('drop', handleDrop, false);
            row.removeEventListener('dragend', handleDragEnd, false);
            row.addEventListener('dragend', handleDragEnd, false);

            if ( ! row.classList.contains('flex-table-header')) {
                row.setAttribute('draggable', true);
            }
        })
    }

    function handleDragStart(e) {
        this.classList.add('moving');

        dragSrcEl = this;

        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.innerHTML);
    }

    function handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }

        e.dataTransfer.dropEffect = 'move';

        return false;
    }

    function handleDragEnter() {
        this.classList.add('over');
    }

    function handleDragLeave() {
        this.classList.remove('over');
    }

    function handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        // Don't do anything if dropping the same column we're dragging.
        if (dragSrcEl != this) {
            var tracks = scope.playlist.tracks.slice(),
                orderedTracks = getOrderedTrackIds(),
                payload = {};

            moveArrayElement(dragSrcEl.getAttribute('trackId'), this.getAttribute('trackId'), orderedTracks);

            angular.forEach(tracks, function(track) {
                angular.forEach(orderedTracks, function(id, i) {
                    if (track.id == id) {
                        track.pivot.position = i+1;
                        orderedTracks[i] = track;
                        payload[track.id] = track.pivot.position;
                    }
                });
            });

            scope.$apply(function() {
                scope.playlist.tracks = orderedTracks;
            });

            $http.put('playlist/'+scope.playlist.id+'/update-order', {orderedIds: payload});
        }

        return false;
    }

    function handleDragEnd() {
        angular.forEach(rows, function (row) {
            row.classList.remove('over');
            row.classList.remove('moving');
            row.style.opacity = '';
        });
    }

    function getOrderedTrackIds() {
        ids = [];
        angular.forEach(document.querySelectorAll('.track-row'), function(node) {
            ids.push(node.getAttribute('trackId'));
        });

        return ids;
    }

    function moveArrayElement(element, insertAfter, array) {
        var oldIndex   = array.indexOf(element),
            newIndex   = array.indexOf(insertAfter) + 1;

        if (newIndex >= array.length || oldIndex < newIndex) {
            newIndex--;
        }

        array.splice(newIndex, 0, array.splice(oldIndex, 1)[0]);
    }
});
