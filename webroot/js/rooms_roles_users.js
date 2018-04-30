/**
 * @fileoverview RoomsRolesUsers Javascript
 * @author nakajimashouhei@gmail.com (Shohei Nakajima)
 */


/**
 * RoomsRolesUsers Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $http)} Controller
 */
NetCommonsApp.controller('RoomsRolesUsers',
    ['$scope', '$http', 'ajaxSendPost', function($scope, $http, ajaxSendPost) {

      /**
       * アクションURL
       */
      $scope.actionUrl = null;

      /**
       * トークン情報取得
       */
      $scope.tokenData;

      /**
       * ルームID
       */
      $scope.roomId;

      /**
       * initialize
       */
      $scope.initialize = function(actionUrl, roomId, token) {
        $scope.actionUrl = actionUrl;
        $scope.roomId = roomId;
        $scope.tokenData = token;
      };

      /**
       * チェックボックスの全選択・全解除
       */
      $scope.allCheck = function($event) {
        var elements = $('input[type="checkbox"]');

        for (var i = 0; i < elements.length; i++) {
          if (elements[i].name) {
            elements[i].checked = $event.currentTarget.checked;
            $scope[elements[i].id] = $event.currentTarget.checked;
          }
        }
      };

      /**
       * チェックボックスクリック
       */
      $scope.check = function(id, checked) {
        $scope[id] = checked;
      };

      /**
       * 保存処理
       */
      $scope.save = function(userId, roleKey) {
        var elements = $('#' + roleKey)[0];

        var postData = {
          RolesRoomsUser: {
            user_id: userId,
            role_key: elements.value
          },
          Room: {
            id: $scope.roomId
          },
          _Token: $scope.tokenData._Token
        };

        //POSTリクエスト
        ajaxSendPost('PUT', $scope.actionUrl + '.json', postData)
            .success(function(response) {
            })
            .error(function(response) {
              //エラー処理
            });
      };

      /**
       * 保存処理
       */
      $scope.delete = function(userId, roleKey) {
        $('#' + roleKey)[0].value = '';
        $scope[roleKey] = '';
        $scope.save(userId, roleKey);
      };

    }]);
