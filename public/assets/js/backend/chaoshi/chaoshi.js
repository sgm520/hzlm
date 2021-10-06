define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'upload'], function ($, undefined, Backend, Table, Form, Upload) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "chaoshi/chaoshi/index",
                    "add_url": "chaoshi/chaoshi/add",
                    "edit_url": "chaoshi/chaoshi/edit",
                    "del_url": "chaoshi/chaoshi/del",
                    "multi_url": "chaoshi/chaoshi/multi",
                    "table": 'chaoshi'
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'chaoshi.create_time',
                columns: [
                    [{
                        checkbox: true
                    },
                        {field: 'id', title: 'ID',operate:false},
                        {field: 'name', title: __('名称'),operate: 'LIKE'},
                        {field: 'category.name', title: __('类别'),searchList: $.getJSON("ajax/chaoshicategory")},
                        {field: 'logo', title: __('logo'), events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'loan_label', title: __('产品标签'),searchList: {1:'放水',2:'爆款',3:'最新'},visible:false},
                        {field: 'loan_label_str', title: __('产品标签'),operate:false},
                        {field: 'guishu', title: __('甲方'),operate:false},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.toggle,searchList: {1:'上架中',2:'已下架'}},
                        {field: 'sort', title: __('排序'),operate: false},
                        {field: 'update_time', title: __('编辑时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'create_time', title: __('创建时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],
                commonSearch: true
            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后

            $(document).on("click", ".btn-approve", function () {
                var data = table.bootstrapTable('getSelections');
                var ids = [];
                if (data.length === 0) {
                    Toastr.error("请选择操作信息");
                    return;
                }
                for (var i = 0; i < data.length; i++) {
                    ids[i] = data[i]['id']
                }
                Layer.confirm(
                    '确认选中'+ids.length+'条更新时间吗?',
                    {icon: 3, title: __('Warning'), offset: '40%', shadeClose: true},
                    function (index) {
                        Layer.close(index);
                        Backend.api.ajax({

                            url: "chaoshi/chaoshi/batchUpdateTime",
                            data: {ids:ids}
                        }, function(data, ret){//成功的回调
                            if (ret.code === 1) {
                                table.bootstrapTable('refresh');
                                Layer.close(index);
                            } else {
                                Layer.close(index);
                                Toastr.error(ret.msg);
                            }
                        }, function(data, ret){//失败的回调
                            console.log(ret);
                            // Toastr.error(ret.msg);
                            Layer.close(index);
                        });
                    }
                );
            });
            // 给表单绑定事件
            Form.api.bindevent($("#update-form"), function () {

            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on('click', "input[name='row[status]']", function () {
                    var name = $("input[name='row[status]']");
                    console.log(name)
                    name.prop("placeholder", $(this).val() == 1 ? name.data("placeholder-menu") : name.data("placeholder-node"));
                });
                $("input[name='row[status]']:checked").trigger("click");
                Form.api.bindevent($("form[role=form]"));
            },
            formatter:{
                status: function (value, row, index) {
                    return '<a href="' + row.fullurl + '" target="_blank" class="label bg-green">' + row.url + '</a>';
                }
            }
        }
    };
    return Controller;
});
