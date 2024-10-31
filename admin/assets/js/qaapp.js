// Your app's root module...
  var qaapp = angular.module('qaapp', [], function($httpProvider) {
    // Use x-www-form-urlencoded Content-Type
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';

    /**
     * The workhorse; converts an object to x-www-form-urlencoded serialization.
     * @param {Object} obj
     * @return {String}
     */
    var param = function(obj) {
      var query = '', name, value, fullSubName, subName, subValue, innerObj, i;

      for(name in obj) {
        value = obj[name];

        if(value instanceof Array) {
          for(i=0; i<value.length; ++i) {
            subValue = value[i];
            fullSubName = name + '[' + i + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        }
        else if(value instanceof Object) {
          for(subName in value) {
            subValue = value[subName];
            fullSubName = name + '[' + subName + ']';
            innerObj = {};
            innerObj[fullSubName] = subValue;
            query += param(innerObj) + '&';
          }
        }
        else if(value !== undefined && value !== null)
          query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
      }

      return query.length ? query.substr(0, query.length - 1) : query;
    };

    // Override $http service's default transformRequest
    $httpProvider.defaults.transformRequest = [function(data) {
      return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
    }];
  });

       qaapp.controller("LinkController", function($scope, $http) {

         $scope.myForm = {};
         $scope.qa_message_class = 'qa-hide' ;

       $scope.myForm.submitTheForm = function(item, event) {

        if( '' == $scope.myForm.qa_link_title || '' == $scope.myForm.qa_link_url){
          return false;
        }
         var dataObject = {
            action : 'quick_admin_save_link'
            ,qa_title : $scope.myForm.qa_link_title
            ,qa_url  : $scope.myForm.qa_link_url
            ,qa_open_new  : $scope.myForm.qa_link_open_new
            ,qa_menu_order  : $scope.myForm.qa_link_menu_order
            ,qa_link_id  : $scope.myForm.qa_link_id
            ,qa_form_action  : $scope.myForm.qa_form_action
         };

         var responsePromise = $http.post(ajaxurl, dataObject, {});
         responsePromise.success(function(dataFromServer, status, headers, config) {
            if ( 1 == dataFromServer.success ) {
              $scope.resetForm();
              $scope.loadData();
              // alert('Submitted successfully');
            };
          });
           responsePromise.error(function(data, status, headers, config) {
             alert("Submitting form failed!");
          });

       }

       $scope.availableHeaders = [];
       $scope.linkData = [];

       // Function to reset form
       $scope.resetForm = function(){
        $scope.myForm.qa_link_title      = '';
        $scope.myForm.qa_link_url        = '';
        $scope.myForm.qa_link_menu_order = '';
        $scope.myForm.qa_link_open_new   = 0;
        $scope.myForm.qa_form_action     = 'add';
        $scope.myForm.qa_link_id         = -1;
        $scope.buttonText                = 'Add';
       };

       $scope.btnDeleteForm = function(obj){

        var confirmation = confirm('Are you sure?');
        if (!confirmation) {
          return false ;
        };

        var datPost = {
         action : 'quick_admin_delete_link'
         ,link_id : obj.link_id
        };

        $http({
            method: 'POST',
            url: ajaxurl,
            data: datPost
          }).success(function(data, status) {
              if (1 == data.success ) {
                // alert('Deleted successfully');
                $scope.resetForm();
                $scope.loadData();
              };
          }).error(function(data, status) {
             alert('Error');
          });

       }

       $scope.btnEditForm = function(obj){
        $scope.myForm.qa_link_title      = obj.title;
        $scope.myForm.qa_link_url        = obj.href;
        $scope.myForm.qa_link_menu_order = obj.menu_order;
        if ( 1 == obj.open_new ) {
          $scope.myForm.qa_link_open_new   = '1';
        }
        else{
          $scope.myForm.qa_link_open_new   = '0';
        }
        $scope.myForm.qa_form_action     = 'edit';
        $scope.myForm.qa_link_id         = obj.link_id;
        $scope.buttonText                = 'Update';
       };

       $scope.loadData = function () {
         var datPost = {
          action: 'quick_admin_get_link_list'
         };

         $http({
             method: 'POST',
             url: ajaxurl,
             data: datPost
           }).success(function(data, status) {
              $scope.linkData = data.data;
           }).error(function(data, status) {
              alert('Error');
           });
         };

         //Initial loading
         $scope.loadData();
         $scope.resetForm();


    });
