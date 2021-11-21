define(['jquery', 'bootstrap', 'backend', 'csmtable', 'form','fixedcolumns','tablestickyheader','tablereorderrows','tabletreegrid','xeditable2'],
    function ($, undefined, Backend, Table, Form,fixedcolumns,tablestickyheader,tablereorderrows,tabletreegrid,xeditable2) {

    var Controller = {
        index: function () {

            // 初始化表格参数配置
            Table.api.init({
                search: true,
                advancedSearch: true,
                pagination: true,
                extend: {
                    "index_url": "fanyong/fanyongorder/index",
                    "table": 'fanyong_order'
                }
            });

            var table = $("#table");
            $(document).on("click", ".multi_refuse", function () {
                var data = table.bootstrapTable('getSelections');
                var ids = [];
                if (data.length === 0) {
                    Toastr.error("请选择操作信息");
                    return;
                }
                for (var i = 0; i < data.length; i++) {
                    ids[i] = data[i]['id'];
                }



                Layer.confirm(
                    '确认选中的' + ids.length + '改为拒绝状态吗?', {
                        icon: 3,
                        title: __('Warning'),
                        offset: '40%',
                        shadeClose: true
                    },
                    function (index) {
                        Layer.close(index);
                        Backend.api.ajax({
                            url: "fanyong/fanyongorder/multi_refuse",
                            data: {
                                ids: ids
                            }
                        }, function (data, ret) { //成功的回调
                            if (ret.code === 1) {

                                table.bootstrapTable('refresh');
                                Layer.close(index);
                            } else {
                                Layer.close(index);
                                Toastr.error(ret.msg);
                            }
                        }, function (data, ret) { //失败的回调
                            console.log(ret);
                            // Toastr.error(ret.msg);
                            Layer.close(index);
                        });
                    }
                );
            })



            var colums=  [

                {field: 'configjson', title: __('数据'),operate: 'LIKE',visible: false},
                {field: 'fanyong.name', title: __('产品名字'), operate: 'LIKE'},
                {field: 'fanyong.logo', title: __('产品logo'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                {field: 'json.tu1', title: __('示例图1'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                {field: 'json.tu2', title: __('示例图2'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                {field: 'json.tu3', title: __('示例图3'),operate:false,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                // {field: 'xlines', title: __('下款额度'),operate: false},
                {field: 'fmoney', title: __('返佣金额'),operate: false},

                {field: 'user_ip', title: __('ip'),operate: false},
                {field: 'ment', title: __('设备'),operate: false},
                {field: 'status_str', title: __('状态'),operate:false,
                    formatter:Table.api.formatter.flag,
                    custom: {'未通过': "danger", '已结算': "success", '审核中': "info"}
                },
                {field: 'status', title: __('状态'),visible:false, searchList: {1: __('已结算'), 0: __('未通过'),2:"审核中"}},
                {field: 'time', title: __('申请时间'), formatter: Table.api.formatter.datetime, operate: 'RANGE', addclass: 'datetimerange', sortable: true},
                {field: 'operate', title: __('Operate'), table: table,
                    events: Table.api.events.operate,
                    buttons: [
                        {
                            name: 'agree',
                            text: __('同意'),
                            icon: 'fa fa-check',
                            classname: 'btn btn-info btn-xs btn-detail btn-dialog',
                            url: 'fanyong/fanyongorder/agree',
                            visible:function (data) {
                                if(data.status ==1 &&  Config.adminId ==1){
                                    return  true
                                }else{
                                    return  false
                                }
                            }
                        },
                        {
                            name: 'refuse',
                            text: __('拒绝'),
                            title: __('拒绝'),
                            classname: 'btn btn-xs btn-danger btn-magic btn-ajax',
                            icon: 'fa fa-close',
                            url: 'fanyong/fanyongorder/refuse',
                            confirm: '你确定要拒绝?',
                            success:function(){
                                table.bootstrapTable('refresh', {});
                                return true;
                            },
                            visible:function (data) {
                                if(data.status ==1 &&  Config.adminId ==1){
                                    return  true
                                }else{
                                    return  false
                                }
                            }
                        },],
                    formatter: Table.api.formatter.operate
                }
            ]

            const columnsObject=Config.column;

            for(let key  in columnsObject){

                colums.unshift({
                    field:'configjson.'+key,
                    title:columnsObject[key],
                    operate:false
                })
            }
            colums.unshift(  {field: 'pid', title: __('代理ID')})
            colums.unshift( {checkbox: true})

            console.log(colums)
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'time',
                columns:[colums] ,
                commonSearch: true,

            });

            // 为表格绑定事件
            Table.api.bindevent(table);//当内容渲染完成后
            // 给表单绑定事件
            Form.api.bindevent($("#update-form"), function () {

            });
        },
        add: function () {
            Controller.api.bindevent();
        },
        authorization:function(){
            Controller.api.bindevent();
        },
        refuse:function(){
            Controller.api.bindevent();
        },
        agree:function (){
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});
