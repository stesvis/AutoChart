/**
 * Created by stesv on 2017-03-10.
 */




var app = angular.module('AdminCategoryApp', ['ngMaterial'])
    .controller('AdminCategoryController', AdminCategoryController);

function AdminCategoryController($scope, $mdDialog, $http) {
    $scope.status = '  ';
    $scope.showConfirm = function (ev, categoryId) {

        // Appending dialog to document.body to cover sidenav in docs app
        var confirm = $mdDialog.confirm()
            .title('Would you like to delete this Service?')
            .textContent('You will not be able to access this service anymore.')
            // .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Yes')
            .cancel('No');

        $mdDialog.show(confirm)
            .then(
                function () {
                    //code in case they click YES

                    console.log('submitting form');
                    $http({
                        url: "/categories/" + categoryId + "/delete",
                        method: "DELETE",
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        data: $.param(categoryId)
                    })
                        .then(
                            function (success) {
                                console.log(success);
                                // location.reload();
                            }, function (error) {
                                console.log(error);
                            });
                }, function () {
                    //code in case they click NO
                });
    };
}
//AdminCategoryController.$inject = ['$scope', '$mdDialog', '$http', '$route'];

