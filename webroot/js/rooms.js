/**
 * @fileoverview UserRoles Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * Rooms controller
 */
NetCommonsApp.controller('RoomsController',
    ['$scope', 'NetCommonsModal', '$location', '$http', 'NC3_URL',
      function($scope, NetCommonsModal, $location, $http, NC3_URL) {

        /**
         * 初期処理　ルームの並び順情報と現在表示中のスペースIDを保持する
         *
         * @return {void}
         */
        $scope.initialize = function(spaeId, roomOrderList) {
          $scope.activeSpaveId = spaeId;
          $scope.roomOrderList = roomOrderList;
        };

        /**
         * ルーム並び順情報から指定のルームIDの情報を返す
         *
         * @return {Object}
         */
        $scope.getRoomInfo = function(roomId) {
          var ret = null;
          angular.forEach($scope.roomOrderList, function(obj, key) {
            if (obj.roomId == roomId) {
              ret = obj;
            }
          });
          return ret;
        };

        /**
         * ルーム並び順情報から指定の親IDと並び順のルーム情報を返す
         *
         * @return {Object}
         */
        $scope.getRoomByParentAndOrder = function(parentId, order) {
          var ret = null;
          angular.forEach($scope.roomOrderList, function(obj, key) {
            if (obj.parentId == parentId && obj.order == order) {
              ret = obj;
            }
          });
          return ret;
        };

        /**
         * 上ボタン、下ボタンが操作可能かどうかを返す
         *
         * @return {bool}
         */
        $scope.isUpDisabled = function(roomId) {
          var obj = $scope.getRoomInfo(roomId);
          if (obj.order == 1) { // 一番上は上ボタン操作不可
            return true;
          }
          return false;
        };
        $scope.isDownDisabled = function(roomId) {
          var obj = $scope.getRoomInfo(roomId);
          if (obj.order == obj.siblings) { // 一番下は下ボタン操作不可
            return true;
          }
          return false;
        };

        /**
         * 上下移動リクエストをControllerに投げる
         * 成功が返されたら画面の描画変更をする
         *
         * @return {void}
         */
        $scope.orderReq = function(direction, srcRoomId, dstRoomId,
                                   parentId, beforeIdx, tgtIdx, beforeTr, tgtTr) {
          var url = NC3_URL + '/rooms/rooms/order/' + $scope.activeSpaveId + '/' +
                    srcRoomId + '/dir:' + direction;
          $http.get(url).then(function(response) {
            $scope.changeIndex(parentId, beforeIdx, tgtIdx);
            if (direction == 'moveUp') {
              beforeTr.insertBefore(tgtTr);
            } else {
              beforeTr.insertAfter(tgtTr);
            }
            $scope.moveSubRoom(srcRoomId);
          }, function(response) {
          });
        };

        /**
         * 上ボタンクリック時処理
         *
         * @return {void}
         */
        $scope.moveUp = function($event, roomId) {
          var room = $scope.getRoomInfo(roomId);
          var parentId = room.parentId;
          var beforeIdx = parseInt(room.order);
          var afterIdx = beforeIdx - 1;
          var beforeTr = $('tr[data-room-id=' + roomId + ']');
          var afterRoom = $scope.getRoomByParentAndOrder(parentId, afterIdx);
          var afterTr = $('tr[data-room-id=' + afterRoom.roomId + ']');
          $scope.orderReq('moveUp',
              roomId, afterRoom.roomId, parentId, beforeIdx, afterIdx, beforeTr, afterTr);
          $event.preventDefault();
          $event.stopPropagation();
          return;
        };
        /**
         * 下ボタンクリック時処理
         *
         * @return {void}
         */
        $scope.moveDown = function($event, roomId) {
          var room = $scope.getRoomInfo(roomId);
          var parentId = room.parentId;
          var beforeIdx = parseInt(room.order);
          var afterIdx = beforeIdx + 1;
          var beforeTr = $('tr[data-room-id=' + roomId + ']');
          var afterRoom = $scope.getRoomByParentAndOrder(parentId, afterIdx);
          var tgtTr = $scope.getLastRow(afterRoom);
          $scope.orderReq('moveDown',
              roomId, afterRoom.roomId, parentId, beforeIdx, afterIdx, beforeTr, tgtTr);
          $event.preventDefault();
          $event.stopPropagation();
          return;
        };

        /**
         * 上下移動に合わせて内部で保持するルーム並び順情報を変更する
         *
         * @return {void}
         */
        $scope.changeIndex = function(parentId, beforeIdx, afterIdx) {
          angular.forEach($scope.roomOrderList, function(obj, key) {
            if (obj.parentId == parentId) {
              if (obj.order == beforeIdx) {
                $scope.roomOrderList[key].order = afterIdx;
              } else if (obj.order == afterIdx) {
                $scope.roomOrderList[key].order = beforeIdx;
              }
            }
          });
        };

        /**
         * 指定のルームにおける画面上での最終行を返す
         * (サブルームが連なる場合があるので、指定ルーム行ではなく「グループ」で見なくてはならない）
         *
         * @return {TR}
         */
        $scope.getLastRow = function(roomInfo) {
          var targetRoomTr = $('tr[data-room-id=' + roomInfo.roomId + ']');
          var subRoomTr = $('tr[data-parent=' + roomInfo.roomId + ']');
          var retTr = null;
          if (subRoomTr.length > 0) {
            retTr = angular.element(subRoomTr[subRoomTr.length - 1]);
          } else {
            retTr = targetRoomTr;
          }
          return retTr;
        };

        /**
         * 指定のルームにサブルームがある場合、それらも指定ルーム業の下へ移動させる
         *
         * @return {void}
         */
        $scope.moveSubRoom = function(targetRoomId) {
          var targetRoomTr = $('tr[data-room-id=' + targetRoomId + ']');
          var subRoomTr = $('tr[data-parent=' + targetRoomId + ']');
          angular.forEach(subRoomTr, function(tr, i) {
            tr = angular.element(tr);
            tr.insertAfter(targetRoomTr);
            targetRoomTr = tr;
          });
        };

        /**
         * ルーム詳細表示
         *
         * @return {void}
         */
        $scope.showRoom = function(spaceId, roomId, tab, isEdit) {
          var url = NC3_URL + '/rooms/rooms/view/' + spaceId + '/' + roomId;
          var search = {};
          if (tab) {
            search['tab'] = tab;
          }
          search['isEdit'] = isEdit;

          $location.search(search);

          NetCommonsModal.show($scope, 'RoomsView', url + $location.url());
        };
      }]);


/**
 * RoomsView modal controller
 */
NetCommonsApp.controller('RoomsView',
    ['$scope', '$uibModalInstance', function($scope, $uibModalInstance) {

      /**
       * dialog cancel
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $uibModalInstance.dismiss('cancel');
      };
    }]);
