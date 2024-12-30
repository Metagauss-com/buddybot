<?php

namespace BuddyBot\Admin\Requests;

final class VectorStore extends \BuddyBot\Admin\Requests\MoRoot
{

    public function requestJs()
    {
        $this->vectorStoreDataJs();
        $this->createVectorStoreJs();
        $this->checkVectorStoreJs();
        $this->getVectorStoreJs();
        $this->displayVectorStoreName();
        $this->deleteVectorStoreJs();
        $this->checkFileStatusOnVectorStoreJs();
        $this->syncBtnJs();
        $this->isFileWritableJs();
        $this->addDataToFileJs();
        $this->transferDataFileJs();
       // $this->getFilesJs();
        $this->deleteOldFilesJs();
        $this->uploadFileIdsOnVectorStoreJs();
    }

    private function vectorStoreDataJs()
    {
        $hostname = wp_parse_url(home_url(), PHP_URL_HOST);
        echo '
            function vectorstoreData() {
                let vectorstoreData = {};
                vectorstoreData["name"] = "' . esc_js($hostname) . '";

                return vectorstoreData;
            }

        ';
    }

    private function createVectorStoreJs()
    {
        $nonce = wp_create_nonce('create_vectorstore');
        $nonce_retrieve = wp_create_nonce('retrieve_vectorstore');
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        echo '
            let pageReload = true;
            
            $("#buddybot-vectorstore-create").click(function() {
                hideAlert();
                disableFields(true);
                showBtnLoader("#buddybot-vectorstore-create");
                pageReload = false;
                checkVectorStore(pageReload);
            });

            const vectorStoreData = ' . wp_json_encode($vectorstore_data) . ';
            function checkVectorStore(pageReload){
                if (vectorStoreData && vectorStoreData.id) {
                    retrieveVectorStore(vectorStoreData.id, pageReload);
                } else {
                    createVectorStore();
                }
            }
             retrieveVectorStore(vectorStoreData.id, pageReload)
            function retrieveVectorStore(vectorstore_id, pageReload){
                const data = {
                    "action": "retrieveVectorStore",
                    "vectorstore_id": vectorstore_id,
                    "nonce": "' . esc_js($nonce_retrieve) . '"
                };
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        if(pageReload){
                            deleteVectorStoreDatabase(); 
                            // displayVectorStoreName();
                        } else {
                            createVectorStore();
                        }
                    } else {
                        if(!pageReload){
                            showAlert(response.message);
                        } else {
                         displayVectorStoreName();
                        }
                        
                    }
                });
            }

            function deleteVectorStoreDatabase(){
               const data = {
                    "action": "deleteVectorStoreDatabase",
                    "nonce": "' . esc_js(wp_create_nonce('delete_vectorstore_database')) . '"
                };

                $.post(ajaxurl, data, function(response) {
                    hideBtnLoader("#buddybot-vectorstore-create");
                    response = JSON.parse(response);
                    if (response.success) {
                        $("#buddybot-assistants-loading-spinner").addClass("visually-hidden");
                        $("#buddybot-vectorstoreName").show();
                        $("#buddybot-vectorstoreName").html(response.message);
                        $("#buddybot-vectorstore-section").addClass("notice notice-error").removeClass("notice-warning");
                        $("#buddybot-vectorstore-create").removeClass("visually-hidden"); 
                    } else {
                        displayVectorStoreName();
                    }
                });
            }

            function createVectorStore(){
                let storeData = vectorstoreData();

                const data = {
                    "action": "createVectorStore",
                    "vectorstore_data": JSON.stringify(storeData),
                    "nonce": "' . esc_js($nonce) . '"
                };
        
                $.post(ajaxurl, data, function(response) {
                    hideBtnLoader("#buddybot-vectorstore-create");
                    response = JSON.parse(response);
                    if (response.success) {
                        displayVectorStoreName();
                        $("#buddybot_vector_store_id").val(response.result.id);
                        $("#buddybot-vectorstore-create").addClass("visually-hidden");
                    } else {
                        showAlert(response.message);
                    }
                    disableFields(false);
                });
            }

        ';
    }

    private function checkVectorStoreJs()
    {
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        echo '
            hideCreateButton();
            function hideCreateButton() {
                const vectorStoreData = ' . wp_json_encode($vectorstore_data) . ';
                if (!vectorStoreData || !vectorStoreData.id) {
               $("#buddybot-vectorstore-create").removeClass("visually-hidden");
                }
            }
        ';
    }

    private function getVectorStoreJs()
    {
        $nonce = wp_create_nonce('get_vectorstore');
        echo '
            //getVectorStore();
           function getVectorStore() {
                hideAlert();
                const data = {
                    "action": "getVectorStore",
                    "nonce": "' . esc_js($nonce) . '"
                };
  
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                    $(".buddybot-msgs").removeClass("visually-hidden");
                    $(".buddybot-msgs").append(response.html);
                    } else {
                        showAlert(response.message);
                    }
                });
            }
        ';
    }

    private function displayVectorStoreName()
    {
        $nonce = wp_create_nonce('display_vectorstore_name');
        echo '
            //displayVectorStoreName();
            function displayVectorStoreName() {
                const data = {
                    "action": "displayVectorStoreName",
                    "nonce": "' . esc_js($nonce) . '"
                };
    
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
    
                    if (response.success) {
                        $("#buddybot-vectorstoreName").html(response.message);
                        $("#buddybot-vectorstore-section").addClass("notice notice-success").removeClass("notice-warning notice-error");
                    } else {
                        $("#buddybot-vectorstoreName").html(response.message);
                        $("#buddybot-vectorstore-section").addClass("notice notice-warning").removeClass("notice-success notice-error");
                    }
                    $("#buddybot-assistants-loading-spinner").addClass("visually-hidden");
                    $("#buddybot-vectorstoreName").show();
                });
            }
        ';
    }

    private function deleteVectorStoreJs()
    {
        $nonce = wp_create_nonce('delete_vectorstore');
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = isset($vectorstore_data['id']) ? $vectorstore_data['id'] : '';
        echo '
            $("#buddybot-vectorstore-delete").click(deleteVectorStore);
                
            function deleteVectorStore(){
                hideAlert();
                disableFields(true);
                showBtnLoader("#buddybot-vectorstore-delete");

                let vectorStoreId = "' . esc_js($vectorstore_id) . '";

                const data = {
                    "action": "deleteVectorStore",
                    "vectorstore_id": vectorStoreId,
                    "nonce": "' . esc_js($nonce) . '"
                };
    
                $.post(ajaxurl, data, function(response) {
                    hideBtnLoader("#buddybot-vectorstore-delete");
                    response = JSON.parse(response);

                    if (response.success) {
                    } else {
                        showAlert(response.message);
                    }
                });
            };
        ';
    }

    private function checkFileStatusJs()
    {
        $nonce = wp_create_nonce('check_file_status');
        echo '
        $(".list-group-item").each(function(){
            let listItem = $(this);
            let dataType = listItem.attr("data-buddybot-type");
            let fileId = listItem.attr("data-buddybot-remote_file_id");

            if (fileId == 0) {
                listItem.find(".buddybot-remote-file-status").text("Not syncronized.");
            } else {
                const data = {
                    "action": "checkFileStatus",
                    "file_id": fileId,
                    "nonce": "' . esc_js($nonce) . '"
                };
      
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    listItem.find(".buddybot-remote-file-status").text(response.message);   
                });
            }

        });
        ';
    }

    private function syncBtnJs()
    {
        echo '
        $(".buddybot-sync-btn").click(syncBtn);
        
        function syncBtn() {
            let dataType = $(this).attr("data-buddybot-type");
            syncBtnStart(dataType);
            isFileWritable(dataType);
        }
        
        function syncBtnStart(dataType) {
            let btn = $("button[data-buddybot-type = " + dataType + "]");
            btn.prop("disabled", true);
            btn.addClass("bb-btn-sync-start");
        }
        
        function syncBtnStop(dataType) {
            let btn = $("button[data-buddybot-type = " + dataType + "]");
            btn.prop("disabled", false);
            btn.removeClass("bb-btn-sync-start");
        }
        ';

    }

    private function isFileWritableJs()
    {
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = isset($vectorstore_data['id']) ? $vectorstore_data['id'] : '';
        $nonce = wp_create_nonce('is_file_writable');
        echo '
        let vectorStoreId =  $("#buddybot_vector_store_id").val() ? $("#buddybot_vector_store_id").val() : "' . esc_js($vectorstore_id) . '";

        function isFileWritable(dataType) {
            const data = {
                "action": "isBbFileWritable",
                "data_type": dataType,
                "vectorstore_id" : vectorStoreId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    addDataToFile(dataType);
                };

                $(".buddybot-msgs").removeClass("visually-hidden");
                $(".buddybot-msgs").append(response.message);
            });
        }
        ';
    }

    private function addDataToFileJs()
    {
        $nonce = wp_create_nonce('add_data_to_file');
        echo '
        function addDataToFile(dataType) {
            const data = {
                "action": "addDataToFile",
                "data_type": dataType,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    transferDataFile(dataType);
                }

                $(".buddybot-msgs").append(response.message);
            });
        }
        ';
    }

    private function transferDataFileJs()
    {
        $nonce = wp_create_nonce('transfer_data_file');
        echo '
        function transferDataFile(dataType) {
            const data = {
                "action": "transferDataFile",
                "data_type": dataType,
                "nonce": "' . esc_js($nonce) . '"
            };

            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    deleteOldfiles(response.id, dataType);
                }

                $(".buddybot-msgs").append(response.message);
            });
        }
        ';
    }

    private function getFilesJs()
    {
        $nonce = wp_create_nonce('get_files');
        echo '
       // getFiles();
        function getFiles(){
            const data = {
                "action": "getFiles",
                "nonce": "' . esc_js($nonce) . '"
            };
      
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);
                if (response.success) {
                   $("#buddybot-get-files").html(response.html);
                } else {
                    showAlert(response.message);
                }
            });
        };
        ';
    }

    private function deleteOldFilesJs()
    {
        $nonce = wp_create_nonce('delete_Old_Files');
        echo '
            
            function deleteOldfiles(newFileId, dataType){

            const data = {
                "action": "deleteOldFiles",
                "file_Id": newFileId,
                "data_type": dataType,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    uploadFileIdsOnVectorStore(newFileId, dataType);
                } 

                $(".buddybot-msgs").append(response.message);

            });
        };
        ';
    }

    private function uploadFileIdsOnVectorStoreJs()
    {
        $nonce = wp_create_nonce('upload_File_Ids_On_Vector_store');

        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = isset($vectorstore_data['id']) ? $vectorstore_data['id'] : '';
        echo '
            function uploadFileIdsOnVectorStore(newFileId, dataType){

                let vectorStoreId = $("#buddybot_vector_store_id").val() ? $("#buddybot_vector_store_id").val() : "' . esc_js($vectorstore_id) . '";

                const data = {
                    "action": "uploadFileIdsOnVectorStore",
                    "file_id": newFileId,
                    "vectorstore_id": vectorStoreId,
                    "nonce": "' . esc_js($nonce) . '"
                }; 

                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        checkFileStatusOnVectorStoreJs(newFileId,dataType);
                    } 

                    $(".buddybot-msgs").append(response.message);
                    syncBtnStop(dataType);

                });
            }
               
            function getVectorStoreFiles(){

            let vectorStoreId = "' . esc_js($vectorstore_id) . '";

                const data = {
                    "action": "getVectorStoreFiles",
                    "vectorstore_id": vectorStoreId,
                    "nonce": "' . esc_js($nonce) . '"
                }; 

                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                      $("#buddybot-get-files").html(response.html);
                    }

                });
            }
        ';
    }

    private function checkFileStatusOnVectorStoreJs()
    {
        $nonce = wp_create_nonce('check_file_status_On_Vector_Store');
        $vectorstore_data = get_option('buddybot_vectorstore_data');
        $vectorstore_id = isset($vectorstore_data['id']) ? $vectorstore_data['id'] : '';
        echo '
        checkFileStatusOnVectorStoreJs();
        function checkFileStatusOnVectorStoreJs(newFileId,dataType="") {
        $(".list-group-item").each(function(){
            let listItem = $(this);
           // let dataType = listItem.attr("data-buddybot-type");
            let fileId = newFileId || listItem.attr("data-buddybot-remote_file_id");
            let vectorStoreId =  $("#buddybot_vector_store_id").val() ? $("#buddybot_vector_store_id").val() : "' . esc_js($vectorstore_id) . '";

                const data = {
                    "action": "checkFileStatusOnVectorStoreJs",
                    "file_id": fileId,
                    "vectorstore_id": vectorStoreId,
                    "nonce": "' . esc_js($nonce) . '"
                };
      
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);
                    listItem.find(".buddybot-remote-file-status"+dataType).text(response.message); 
                });

        });
        }
        ';
    }
}
