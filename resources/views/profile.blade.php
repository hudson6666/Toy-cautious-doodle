<!-- resources/views/profile.blade.php -->

@extends('layouts.navibar')

@section('title', '个人资料')

@section('content')

    <style>
        .profile-container {
            width: 100%;
            max-width: 1010px;
            display: table;
            margin-left: auto;
            margin-right: auto;
            /*padding-left: 15px;
            padding-right: 15px;*/
        }

        .left-section {
            min-width: 250px;
            float: left;
            padding-left: 10px;
            padding-right: 10px;
        }

        .right-section {
            max-width: 750px;
            float: left;
            padding-left: 10px;
            padding-right: 10px;
        }

        .avatar {
            margin-left: auto;
            margin-right: auto;
            display: table;
            width: 230px;
            height: 230px;
            border-radius: 6px;
        }

        .table-control {
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 3px;
            padding-right: 3px;
        }

        .expand {
            width: 100%;
        }

        .center {
            width: auto;
            display: table;
            margin-left: auto;
            margin-right: auto;
        }

        .text-center {
            text-align: center;
        }

    </style>

    <div class="profile-container">
        <div class="left-section col-xs-12 col-sm-4">
            <img class="avatar" src="/img/avatar.gif" alt="Cautious doodle"/>
            <br/><br/>
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingOne">
                        <h4 class="panel-title">
                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne"
                               aria-expanded="true" aria-controls="collapseOne">
                                我的小梦想
                            </a>
                        </h4>
                    </div>
                    <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel"
                         aria-labelledby="headingOne">
                        <div class="panel-body">
                            <table class="form-horizontal center">
                                <tbody id="goal_ul"></tbody>
                                <tfoot>
                                <tr>
                                    <td class="table-control" colspan="4">
                                        <input class="btn btn-primary expand" type="button" id="goal_smt"
                                               value="添加一个小梦想"/>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading" role="tab" id="headingTwo">
                        <h4 class="panel-title">
                            <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion"
                               href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                已完结的小梦想
                            </a>
                        </h4>
                    </div>
                    <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                        <div class="panel-body">
                            <table class="form-horizontal center">
                                <tbody id="goal_old_ul"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                function loadGoalUl() {
                    $.ajax({
                        type: "GET",
                        url: "/profile/goal",
                        success: function (msg) {
                            $("#goal_ul").empty();
                            $("#goal_old_ul").empty();
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                if (dataObj[i].state == "Pending")
                                    $("#goal_ul").append("<tr><td class='expand table-control table-text'><button class='btn btn-link' onclick='showGoalEdit(\"" + dataObj[i].id + "\")' >" + dataObj[i].title + '</button></td><td class="table-control"><input class="btn btn-info" type="button" onclick="showGoalDel(' + dataObj[i].id + ')" value="Pending" /></td></tr>');
                                else {
                                    var str = "<tr><td class='expand table-control table-text'><button class='btn btn-link' onclick='showGoalEdit(\"" + dataObj[i].id + "\")' >" + dataObj[i].title + '</button></td><td class="table-control"><input class="btn ';
                                    switch (dataObj[i].state) {
                                        case "Accomplished":
                                            str += "btn-success";
                                            break;
                                        case "Failed":
                                            str += "btn-danger";
                                            break;
                                        case "Aborted":
                                            str += "btn-danger";
                                            break;
                                    }
                                    str += ' expand" type="button" value="' + dataObj[i].state + '" /></td></tr>';
                                    $("#goal_old_ul").append(str);
                                }
                            }
                        }
                    });
                }
                function showGoalEdit(id) {
                    $("#goalModalTitle").html("小梦想详情");
                    $("#goal_fm input[type='text']").val("");
                    $("#goal_tasktitle").attr("disabled", true);
                    $("#goal_taskdesc").attr("disabled", true);
                    $("#goal_date").attr("disabled", true);
                    $("#updateGoal_smt").attr("style", "display: none");
                    $.ajax({
                        type: "GET",
                        url: "/profile/goal",
                        success: function (msg) {
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                if (dataObj[i].id == id) {
                                    $("#goal_tasktitle").val(dataObj[i].title);
                                    $("#goal_taskdesc").val(dataObj[i].description);
                                    $("#goal_date").val(dataObj[i].date);
                                }
                            }
                        }
                    });
                    $("#goalEditModal").modal('show');
                }
                function delGoal(state) {
                    var str_data = $("#goal_del_fm input").map(function () {
                        return ($(this).attr("name") + '=' + $(this).val());
                    }).get().join("&");
                    str_data += "&state=" + state;
                    var id = $("#goal_del_id").val();
                    $.ajax({
                        type: "POST",
                        url: "/profile/goal/" + id,
                        data: str_data,
                        success: function (msg) {
                            $("#goalDelModal").modal("hide");
                            loadGoalUl();
                        }
                    });
                }
                function showGoalDel(id) {
                    $("#goal_del_id").val(id);
                    $("#goalDelModal").modal('show');
                }
                $(document).ready(function () {
                    loadGoalUl();
                    $("#updateGoal_smt").click(function () {
                        var str_data1 = $("#goal_fm input").map(function () {
                            return ($(this).attr("name") + '=' + $(this).val());
                        }).get().join("&");
                        var str_data2 = $("#goal_fm textarea").map(function () {
                            return ($(this).attr("name") + '=' + $(this).val());
                        }).get().join("&");
                        var str_data = str_data1 + '&' + str_data2;
                        $.ajax({
                            type: "POST",
                            url: "/profile/goal",
                            data: str_data,
                            success: function (msg) {
                                $("#goalEditModal").modal('hide');
                                loadGoalUl();
                            }
                        });
                    });
                    $("#goal_smt").click(function () {
                        $("#goalModalTitle").html("添加小梦想");
                        $("#goal_fm input[type='text']").val("");
                        $("#goal_fm textarea").val("");
                        $("#goal_fm input[type='date']").val("");
                        $("#updateGoal_smt").text("提交");
                        $("#goal_date").removeAttr("disabled");
                        $("#goal_tasktitle").removeAttr("disabled");
                        $("#goal_taskdesc").removeAttr("disabled");
                        $("#updateGoal_smt").attr("style", "display: inline-block");
                        $("#goalEditModal").modal('show');
                    });
                })
            </script>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        我的昵称
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="form-horizontal center">
                        <tbody id="nickname_ul"></tbody>
                        <tfoot>
                        <tr id="nickname_fm">
                            <td class="table-control expand">
                                {!! csrf_field() !!}
                                <input class="form-control" type="text" name="nickname"
                                       onkeydown="if(event.keyCode==13){$('#nickname_smt').click()}"/>
                            </td>
                            <td class="table-control">
                                <input class="btn btn-primary" type="button" id="nickname_smt" value="添加"/>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <script>
                function loadNicknameUl() {
                    $.ajax({
                        type: "GET",
                        url: "/profile/nickname",
                        success: function (msg) {
                            $("#nickname_ul").empty();
                            $("#nickname_fm input[type='text']").val("");
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                $("#nickname_ul").append("<tr><td class='expand table-control table-text'>" + dataObj[i].nickname + '</td><td class="table-control" id="nickname_del_id_' + dataObj[i].id + '">{{ csrf_field() }} {{ method_field("DELETE") }}<input class="btn btn-danger" type="button" onclick="delNicknameLi(' + dataObj[i].id + ')" value="删除" /></td></tr>');
                            }
                        }
                    });
                }
                function delNicknameLi(id) {
                    var str_query = "#nickname_del_id_" + id.toString() + " input";
                    var str_data = $(str_query).map(function () {
                        return ($(this).attr("name") + '=' + $(this).val());
                    }).get().join("&");
                    $.ajax({
                        type: "POST",
                        url: "/profile/nickname/" + id.toString(),
                        data: str_data,
                        success: function (msg) {
                            loadNicknameUl();
                        }
                    })
                }
                $(document).ready(function () {
                    loadNicknameUl();
                    $("#nickname_smt").click(function () {
                        var str_data = $("#nickname_fm input").map(function () {
                            return ($(this).attr("name") + '=' + $(this).val());
                        }).get().join("&");
                        $.ajax({
                            type: "POST",
                            url: "/profile/nickname",
                            data: str_data,
                            success: function (msg) {
                                loadNicknameUl();
                            }
                        });
                    })
                })
            </script>

        </div>

        <div class="right-section col-xs-12 col-sm-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        常规任务
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="form-horizontal center">
                        <tbody id="rgtask_ul"></tbody>
                        <tfoot>
                        <tr>
                            <td class="table-control" colspan="4">
                                <input class="btn btn-primary expand" type="button" id="rgtask_smt" value="添加一个任务"/>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <script>
                function loadRgtaskUl() {
                    $.ajax({
                        type: "GET",
                        url: "/task/rgtask",
                        success: function (msg) {
                            $("#rgtask_ul").empty();
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                var str = "<tr><td class='expand table-control table-text'><button class='btn btn-link' onclick='showRgtaskEdit(\"" + dataObj[i].id + "\")' >" + dataObj[i].title + '</button></td><td class="table-control"><button class="btn ';
                                if(dataObj[i].type == "activity")
                                    str += "btn-warning";
                                else
                                    str += "btn-default";
                                str += '">' + dataObj[i].family.title + '</button></td><td class="table-control"><input class="btn btn-info" type="button" value="已经坚持' + dataObj[i].day_cnt + '天" /></td><td class="table-control" id="rgtask_del_id_' + dataObj[i].id + '">{{ csrf_field() }} {{ method_field("DELETE") }}<input class="btn btn-danger" type="button" onclick="delRgtaskLi(' + dataObj[i].id + ')" value="删除"/></td></tr>';
                                $("#rgtask_ul").append(str);
                            }
                        }
                    });
                }
                function showRgtaskEdit(id) {
                    $.ajax({
                        type: "GET",
                        url: "/task/family",
                        success: function (msg) {
                            $("#taskfamily").empty();
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                $("#taskfamily").append("<option value=" + dataObj[i].id + ">" + dataObj[i].title + '</option>');
                            }
                        }
                    });
                    $("#rgtaskModalTitle").html("任务详情");
                    $("#rgtask_fm input[type='text']").val("");
                    $("#rgtaskEditId").val(id);
                    $("#startdate").attr("disabled", true);
                    $("#updateRgtask_smt").text("修改");
                    $.ajax({
                        type: "GET",
                        url: "/task/rgtask",
                        success: function (msg) {
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                if (dataObj[i].id == id) {
                                    $("#tasktitle").val(dataObj[i].title);
                                    $("#taskdesc").val(dataObj[i].description);
                                    $("#startdate").val(dataObj[i].startdate);
                                    $("#period").val(dataObj[i].period);
                                    $("#taskday").val(dataObj[i].activeday);
                                    $("#taskfamily").val(dataObj[i].family_id);
                                    $("#tasktype").val(dataObj[i].type);
                                }
                            }
                        }
                    });
                    $("#rgtaskEditModal").modal('show');
                }
                function delRgtaskLi(id) {
                    var str_query = "#rgtask_del_id_" + id.toString() + " input";
                    var str_data = $(str_query).map(function () {
                        return ($(this).attr("name") + '=' + $(this).val());
                    }).get().join("&");
                    $.ajax({
                        type: "POST",
                        url: "/task/rgtask/" + id.toString(),
                        data: str_data,
                        success: function (msg) {
                            loadRgtaskUl();
                        }
                    })
                }
                $(document).ready(function () {
                    loadRgtaskUl();
                    $("#rgtask_smt").click(function () {
                        $.ajax({
                            type: "GET",
                            url: "/task/family",
                            success: function (msg) {
                                $("#taskfamily").empty();
                                var dataObj = eval("(" + msg + ")");
                                for (i in dataObj) {
                                    $("#taskfamily").append("<option value=" + dataObj[i].id + ">" + dataObj[i].title + '</option>');
                                }
                            }
                        });
                        $("#rgtaskModalTitle").html("新增任务");
                        $("#rgtask_fm input[type='text']").val("");
                        $("#rgtask_fm textarea").val("");
                        $("#rgtask_fm input[type='date']").val("{{ $date_today }}");
                        $("#rgtask_fm input[type='number']").val("");
                        $("#startdate").removeAttr("disabled");
                        $("#updateRgtask_smt").text("提交");
                        $("#rgtaskEditModal").modal('show');
                    });
                });
            </script>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        任务分类
                    </h3>
                </div>
                <div class="panel-body">
                    <div id="alertarea"></div>
                    <table class="form-horizontal center">
                        <tbody id="family_ul"></tbody>
                        <tfoot>
                        <tr>
                            <td class="table-control" colspan="4">
                                <input class="btn btn-primary expand" type="button" id="family_smt" value="添加一个分类"/>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <script>
                function loadFamilyUl() {
                    $.ajax({
                        type: "GET",
                        url: "/task/family",
                        success: function (msg) {
                            $("#family_ul").empty();
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                $("#family_ul").append("<tr><td class='expand table-control table-text' id='family_id_" + dataObj[i].id + "'>" + dataObj[i].title + '</td><td class="table-control"><input class="btn btn-primary" type="button" value="修改" onclick="showFamilyEdit(' + dataObj[i].id + ')"/></td><td class="table-control" id="family_del_id_' + dataObj[i].id + '">{{ csrf_field() }} {{ method_field("DELETE") }}<input class="btn btn-danger" type="button" onclick="delFamilyLi(' + dataObj[i].id + ')" value="删除" /></td></tr>');
                            }
                        }
                    });
                }
                function showFamilyEdit(id) {
                    $("#familyModalTitle").html("分类详情");
                    $("#family_fm input[type='text']").val("");
                    $("#familyEditId").val(id);
                    $("#updateFamily_smt").text("修改");
                    $.ajax({
                        type: "GET",
                        url: "/task/family",
                        success: function (msg) {
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                if (dataObj[i].id == id) {
                                    $("#familytitle").val(dataObj[i].title);
                                    $("#familydesc").val(dataObj[i].description);
                                    $("#familydest").val(dataObj[i].destination);
                                }
                            }
                        }
                    });
                    $("#familyEditModal").modal('show');
                }
                function delFamilyLi(id) {
                    var str_query = "#family_del_id_" + id.toString() + " input";
                    var str_data = $(str_query).map(function () {
                        return ($(this).attr("name") + '=' + $(this).val());
                    }).get().join("&");
                    $.ajax({
                        type: "POST",
                        url: "/task/family/" + id.toString(),
                        data: str_data,
                        success: function (msg) {
                            if(msg == "Error: You cannot delete a family with task in it!") {
                                var str = '<div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>警告！</strong>有任务的分类不可被删除。</div>';
                                $('#alertarea').append(str);
                            }
                            if(msg == "Error: You cannot delete this family if it's the only family.") {
                                var str = '<div class="alert alert-warning fade in"><a href="#" class="close" data-dismiss="alert">&times;</a><strong>警告！</strong>你不能删除唯一存在的分类。</div>';
                                $('#alertarea').append(str);
                            }
                            loadFamilyUl();
                        }
                    })
                }
                $(document).ready(function () {
                    loadFamilyUl();
                    $("#family_smt").click(function () {
                        $("#familyModalTitle").html("添加分类");
                        $("#family_fm input[type='text']").val("");
                        $("#family_fm textarea").val("");
                        $("#family_fm input[type='date']").val("");
                        $("#family_fm input[type='number']").val("");
                        $("#updateFamily_smt").text("提交");
                        $("#familyEditModal").modal('show');
                    });
                    $("#updateFamily_smt").click(function () {
                        if ($("#familyModalTitle").html() == "添加分类") {
                            var str_data1 = $("#family_fm input").map(function () {
                                return ($(this).attr("name") + '=' + $(this).val());
                            }).get().join("&");
                            var str_data2 = $("#family_fm textarea").map(function () {
                                return ($(this).attr("name") + '=' + $(this).val());
                            }).get().join("&");
                            var str_data = str_data1 + '&' + str_data2;
                            $.ajax({
                                type: "POST",
                                url: "/task/family",
                                data: str_data,
                                success: function (msg) {
                                    $("#familyEditModal").modal('hide');
                                    loadFamilyUl();
                                    //location.reload();
                                }
                            });
                        }
                        else if ($("#familyModalTitle").html() == "分类详情") {
                            var str_data1 = $("#family_fm input").map(function () {
                                return ($(this).attr("name") + '=' + $(this).val());
                            }).get().join("&");
                            var str_data2 = $("#family_fm textarea").map(function () {
                                return ($(this).attr("name") + '=' + $(this).val());
                            }).get().join("&");
                            var str_data3 = "_method=PUT";
                            var str_data = str_data1 + '&' + str_data2 + '&' + str_data3;
                            var id = $("#familyEditId").val();
                            $.ajax({
                                type: "POST",
                                url: "/task/family/" + id.toString(),
                                data: str_data,
                                success: function (msg) {
                                    $("#familyEditModal").modal('hide');
                                    loadFamilyUl();
                                }
                            });
                        }
                        loadRgtaskUl();
                    });
                })
            </script>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        我的签名
                    </h3>
                </div>
                <div class="panel-body">
                    <table class="form-horizontal center">
                        <tbody id="signature_ul"></tbody>
                        <tfoot>
                        <tr id="signature_fm">
                            <td class="table-control expand" colspan="2">
                                {!! csrf_field() !!}
                                <input class="form-control" type="text" name="signature"
                                       onkeydown="if(event.keyCode==13){$('#signature_smt').click()}"/>
                            </td>
                            <td class="table-control">
                                <input class="btn btn-primary" type="button" id="signature_smt" value="添加"/>
                            </td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <script>
                function loadSignatureUl() {
                    $.ajax({
                        type: "GET",
                        url: "/profile/signature",
                        success: function (msg) {
                            $("#signature_ul").empty();
                            $("#signature_fm input[type='text']").val("");
                            var dataObj = eval("(" + msg + ")");
                            for (i in dataObj) {
                                $("#signature_ul").append("<tr><td class='expand table-control table-text' id='signature_id_" + dataObj[i].id + "'>" + dataObj[i].signature + '</td><td class="table-control"><input class="btn btn-primary" type="button" value="修改" onclick="showSignatureEdit(' + dataObj[i].id + ')"/></td><td class="table-control" id="signature_del_id_' + dataObj[i].id + '">{{ csrf_field() }} {{ method_field("DELETE") }}<input class="btn btn-danger" type="button" onclick="delSignatureLi(' + dataObj[i].id + ')" value="删除" /></td></tr>');
                            }
                        }
                    });
                }
                function showSignatureEdit(id) {
                    var str_query = "#signature_id_" + id.toString();
                    var str = $(str_query).html();
                    $("#signatureEditId").val(id);
                    $("#signatureEditInput").val(str);
                    $("#signatureEditModal").modal('show');
                }
                function delSignatureLi(id) {
                    var str_query = "#signature_del_id_" + id.toString() + " input";
                    var str_data = $(str_query).map(function () {
                        return ($(this).attr("name") + '=' + $(this).val());
                    }).get().join("&");
                    $.ajax({
                        type: "POST",
                        url: "/profile/signature/" + id.toString(),
                        data: str_data,
                        success: function (msg) {
                            loadSignatureUl();
                        }
                    })
                }
                $(document).ready(function () {
                    loadSignatureUl();
                    $("#signature_smt").click(function () {
                        var str_data = $("#signature_fm input").map(function () {
                            return ($(this).attr("name") + '=' + $(this).val());
                        }).get().join("&");
                        $.ajax({
                            type: "POST",
                            url: "/profile/signature",
                            data: str_data,
                            success: function (msg) {
                                loadSignatureUl();
                            }
                        });
                    });
                    $("#updateSignature_smt").click(function () {
                        var str_data = $("#updateSignature_fm input").map(function () {
                            return ($(this).attr("name") + '=' + $(this).val());
                        }).get().join("&");
                        var id = $("#signatureEditId").val();
                        $.ajax({
                            type: "POST",
                            url: "/profile/signature/" + id.toString(),
                            data: str_data,
                            success: function (msg) {
                                $("#signatureEditModal").modal('hide');
                                loadSignatureUl();
                            }
                        });
                    })
                })
            </script>
        </div>
    </div>
    <!-- 模态框（Modal）FamilyEditModal -->
    <div class="modal fade" id="familyEditModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="familyModalTitle">
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="family_fm" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" id="familyEditId"/>
                        <div class="form-group">
                            <label for="familytitle" class="col-sm-2 col-sm-offset-1 control-label">分类名称</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="familytitle" name="title">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="familydesc" class="col-sm-2 col-sm-offset-1 col-xs-12 control-label">分类描述</label>
                            <div class="col-sm-7">
                                <textarea class="form-control" id="familydesc" name="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="familydest" class="col-sm-2 col-sm-offset-1 col-xs-12 control-label">分类目标</label>
                            <div class="col-sm-7">
                                <textarea class="form-control" id="familydest" name="destination"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">关闭
                    </button>
                    <button id="updateFamily_smt" type="button" class="btn btn-primary">
                        提交
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <!-- 模态框（Modal）signatureEditModal -->
    <div class="modal fade" id="signatureEditModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        修改签名
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="updateSignature_fm" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}
                        <input type="hidden" name="id" id="signatureEditId"/>
                        <input class="form-control expand" name="signature" id="signatureEditInput"
                               onkeydown="if(event.keyCode==13){$('#updateSignature_smt').click()}"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">关闭
                    </button>
                    <button id="updateSignature_smt" type="button" class="btn btn-primary">
                        提交
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <!-- 模态框（Modal）GoalEditModal -->
    <div class="modal fade" id="goalEditModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="goalModalTitle">
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="goal_fm" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="goal_tasktitle" class="col-sm-4 control-label">小梦想名称</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="goal_tasktitle" name="title">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="goal_taskdesc"
                                   class="col-sm-4 col-xs-12 control-label">小梦想描述</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" id="goal_taskdesc" name="description"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="goal_date" class="col-sm-4 control-label">预计完成日</label>
                            <div class="col-sm-6">
                                <input type="date" class="form-control" id="goal_date" name="date">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">关闭
                    </button>
                    <button id="updateGoal_smt" type="button" class="btn btn-primary">
                        提交
                    </button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
    <!-- 模态框（Modal）GoalDelModal -->
    <div class="modal fade" id="goalDelModal" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close"
                            data-dismiss="modal" aria-hidden="true">
                        &times;
                    </button>
                    <h4 class="modal-title" id="goalDelModalTitle">
                        完结小梦想
                    </h4>
                </div>
                <div class="modal-body">
                    <div id="goal_del_fm" class="form-horizontal">
                        {{ csrf_field() }}
                        {{ method_field("PUT") }}
                        <input type="hidden" id="goal_del_id"/>
                        <div class="form-group">
                            <div class="col-sm-4">
                                <input type="button" class="btn btn-success expand" onclick="delGoal('Accomplished')"
                                       value="达成">
                            </div>
                            <div class="col-sm-4">
                                <input type="button" class="btn btn-danger expand" onclick="delGoal('Failed')"
                                       value="失败">
                            </div>
                            <div class="col-sm-4">
                                <input type="button" class="btn btn-danger expand" onclick="delGoal('Aborted')"
                                       value="放弃">
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal -->
    </div>
@endsection