app.directive('contenteditable', function($timeout) {
    return {
      restrict: "A",
      priority: 1000,
      scope:{ngModel:"="},
      link: function(scope, element) {
        element.html(scope.ngModel);
        element.on('focus blur keyup paste input', function() {
          scope.ngModel = element.text();
          scope.$apply();
          return element;
        });
      }
    };
  });