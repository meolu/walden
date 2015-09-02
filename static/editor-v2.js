function BlogViewModel() {
    var self = this;
    self.editor  = ko.observable();
    self.blogs   = ko.observableArray([]);
    self.title   = ko.observable();
    self.blog_id = ko.observable();

    var blog_title;
    var textarea = $('textarea');

    self.load = function () {
        blog_id = this.blog_id();

        ajaxRequest("get", '/home/service/loadblog/', { "blog_id": blog_id }, function (data) {
            if (!data.is_success) {
                console.log('load error');
            } else {
                self.editor(data.blog);
                textarea.val(data.blog);
            }
        });
    };

    self.create = function () {
        ajaxRequest("get", '/home/service/createblog/', {}, function (data) {
            if (!data.is_success) {
                console.log('create error');
            } else {
                self.blog_id(data.blog_id);
                self.get(page);
            }
        });
    };

    self.sync = function () {
        ajaxRequest("post", '/home/service/syncblog/', { "blog_id": blog_id, "blog_content": textarea.val()}, function (data) {
            if (!data.is_success) {
                console.log('sync error');
            } else {
                // self.blog.val(self.data);
            }
        });
    };


    self.get = function (page) {
        ajaxRequest("get", '/home/service/getblogs/', {'page' : page}, function (data) {
            if (!data.is_success) {
                console.log('get error');
            } else {
                self.blogs([]);;
                var blogsObj = data.blogs;
                for( r in data.blogs ) {
                    console.log(blogsObj[r].title_name);
                    self.blogs.push(new Task({ title: blogsObj[r].title_name, blog_id: blogsObj[r].blog_id }));
                }

            }
        });
    };

    self.compile = function () {
        ajaxRequest("get", '/home/service/compile/', {}, function (data) {
            if (!data.is_success) {
                console.log('compile error');
            } else {
                // console.log(data.is_success);
            }
        });
    };

    self.prev = function() {
        self.get(--page)
    }

    self.next = function() {
        self.get(++page)
    }

    ajaxRequest = function (type, url, data, callback) { // Ajax helper
        $.ajax({
            url: url,
            data: data,
            type: type,
            dataType: "json",
            success: callback
        });
    };

};

function Task(data) {
    this.title = ko.observable(data.title);
    this.blog_id = ko.observable(data.blog_id);
}

$('#controls').hover(
    function () {
        $(this).attr('class', '');
    },
    function () {
        $(this).addClass('hidden');
    }
)

var sprintf = function() {
    var arg = arguments,
        str = arg[0] || '',
        i, n;
    for (i = 1, n = arg.length; i < n; i++) {
        str = str.replace(/%s/, arg[i]);
    }
    return str;
}

page = 0;
$(function() {
    // Initiate the Knockout bindings
    var view = new BlogViewModel();
    ko.applyBindings(view);
    view.get(page);
});