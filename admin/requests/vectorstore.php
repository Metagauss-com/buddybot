<?php

namespace BuddyBot\Admin\Requests;

final class VectorStore extends \BuddyBot\Admin\Requests\MoRoot
{

    public function requestJs()
    {
        $this->vectorStoreDataJs();
        $this->progressBar();
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
        if($hostname === 'localhost'){
            $path = wp_parse_url(home_url(), PHP_URL_PATH) ?? '';
            $hostname = $hostname . str_replace('/', '.', $path); 
        }
        echo '
            function vectorstoreData() {
                let vectorstoreData = {};
                vectorstoreData["name"] = "' . esc_js($hostname) . '";

                return vectorstoreData;
            }

        ';
    }
      
    private function progressBar(){
        echo'
        function updateProgressBar(percentage, message, isError = false) {
            $("#buddybot-ProgressBar .progress-bar").css("width", percentage + "%").attr("aria-valuenow", percentage);
            $("#buddybot-progressbar-percentage").text(percentage + "%");

            if (isError) {
                $("#buddybot-ProgressBar .progress-bar").removeClass("bg-primary").addClass("bg-danger");
                $("#buddybot-ProgressBar-icon").text("error").css({"color": "red"});
            } else {
                $("#buddybot-ProgressBar .progress-bar").removeClass("bg-danger").addClass("bg-primary");
                $("#buddybot-ProgressBar-icon").text("check_circle").css({"color": "green"});
            }

            $("#buddybot-message").html(message);

        }

        function showProgressBar() {
            $("#buddybot-ProgressBar").animate({ opacity: 1 }, 1000);
            $("#buddybot-progressbar-percentage").animate({ opacity: 1 }, 1000);
            $("#buddybot-sync-msgs").animate({ opacity: 1 }, 1000);
        }

        function hideProgressBar() {
            $("#buddybot-ProgressBar").animate({ opacity: 0 }, 1000);
            $("#buddybot-progressbar-percentage").animate({ opacity: 0 }, 1000);
            $("#buddybot-sync-msgs").animate({ opacity: 0 }, 1000);
            updateProgressBar(100, "' . esc_js(__('Sync Completed', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '");
        }
        ';
    }

    private function createVectorStoreJs()
    {
        $nonce = wp_create_nonce('create_vectorstore');
        $nonce_retrieve = wp_create_nonce('retrieve_vectorstore');
        echo '
            let pageReload = true;
            
            $("#buddybot-vectorstore-create").click(function() {
                $("#buddybot-ProgressBar").css("opacity", 0);
                $("#buddybot-progressbar-percentage").css("opacity", 0);
                $("#buddybot-sync-msgs").css("opacity", 0);
                updateProgressBar(0, "' . esc_js(__('Sync processing...', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '");
                $(".buddybot-sync-btn").prop("disabled",false);
                hideAlert();
                disableFields(true);
                showWordpressLoader("#buddybot-vectorstore-create");
                pageReload = false;
                checkVectorStore(pageReload);
            });

            const vectorStoreId = $("#buddybot_vector_store_id").val();
            function checkVectorStore(pageReload){
                if (vectorStoreId) {
                    retrieveVectorStore(vectorStoreId, pageReload);
                } else {
                    getVectorStore();
                }
            }
             retrieveVectorStore(vectorStoreId, pageReload)
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
                            getVectorStore();
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
                        $("#buddybot-vectorstore-section").addClass("notice notice-error ms-0").removeClass("notice-warning");
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
                    hideWordpressLoader("#buddybot-vectorstore-create");
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
        echo '
            hideCreateButton();
            function hideCreateButton() {
                const vectorStoreId = $("#buddybot_vector_store_id").val();
                if (!vectorStoreId) {
               $("#buddybot-vectorstore-create").removeClass("visually-hidden");
                }
            }
        ';
    }

    private function getVectorStoreJs()
    {
        $nonce = wp_create_nonce('get_vectorstore');
        echo '
           function getVectorStore() {
                const data = {
                    "action": "getVectorStore",
                    "nonce": "' . esc_js($nonce) . '"
                };
  
                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        displayVectorStoreName();
                        $("#buddybot_vector_store_id").val(response.data.id);
                        $("#buddybot-vectorstore-create").addClass("visually-hidden");
                        hideWordpressLoader("#buddybot-vectorstore-create");
                    } else {
                        createVectorStore();
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
                        $("#buddybot-vectorstoreName").hide();
                        $("#buddybot-vectorstore-section").removeClass("notice notice-warning ms-0");
                    } else {
                        $("#buddybot-vectorstoreName").html(response.message);
                        $("#buddybot-vectorstore-section").addClass("notice notice-warning ms-0");
                        $("#buddybot-vectorstoreName").show();
                    }
                });
            }
        ';
    }

    private function deleteVectorStoreJs()
    {
        $nonce = wp_create_nonce('delete_vectorstore');
        echo '
            $("#buddybot-vectorstore-delete").click(deleteVectorStore);
                
            function deleteVectorStore(){
                hideAlert();
                disableFields(true);
                showBtnLoader("#buddybot-vectorstore-delete");

                let vectorStoreId = $("#buddybot_vector_store_id").val();

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
            showProgressBar();
            let dataType = $(this).attr("data-buddybot-type");
            syncBtnStart(dataType);
            isFileWritable(dataType);
        }
        
        function syncBtnStart(dataType) {
            let btn = $("button[data-buddybot-type = " + dataType + "]");
            $(".buddybot-sync-btn").prop("disabled",true);
            btn.addClass("bb-btn-sync-start");
        }
        
        function syncBtnStop(dataType) {
            let btn = $("button[data-buddybot-type = " + dataType + "]");
            $(".buddybot-sync-btn").prop("disabled",false);
            btn.removeClass("bb-btn-sync-start");
        }
        ';

    }

    private function isFileWritableJs()
    {
        $nonce = wp_create_nonce('is_file_writable');
        echo '

        function isFileWritable(dataType) {
        let vectorStoreId =  $("#buddybot_vector_store_id").val();
            const data = {
                "action": "isBbFileWritable",
                "data_type": dataType,
                "vectorstore_id" : vectorStoreId,
                "nonce": "' . esc_js($nonce) . '"
            };
  
            $.post(ajaxurl, data, function(response) {
                response = JSON.parse(response);

                if (response.success) {
                    updateProgressBar(25, response.message);
                    addDataToFile(dataType);
                } else {
                    updateProgressBar(15, response.message, true);
                }
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
                    updateProgressBar(50, response.message);
                    transferDataFile(dataType);
                } else {
                    updateProgressBar(25, response.message, true);
                }
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
                    updateProgressBar(72, response.message);
                    deleteOldfiles(response.id, dataType);
                } else {
                    updateProgressBar(50, response.message, true);
                }
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
                    updateProgressBar(84, response.message);
                    uploadFileIdsOnVectorStore(newFileId, dataType);
                } else {
                    updateProgressBar(72, response.message, true);
                }

            });
        };
        ';
    }

    private function uploadFileIdsOnVectorStoreJs()
    {
        $nonce = wp_create_nonce('upload_File_Ids_On_Vector_store');

        echo '
            function uploadFileIdsOnVectorStore(newFileId, dataType){

                let vectorStoreId = $("#buddybot_vector_store_id").val();

                const data = {
                    "action": "uploadFileIdsOnVectorStore",
                    "file_id": newFileId,
                    "vectorstore_id": vectorStoreId,
                    "data_type": dataType,
                    "nonce": "' . esc_js($nonce) . '"
                }; 

                $.post(ajaxurl, data, function(response) {
                    response = JSON.parse(response);

                    if (response.success) {
                        updateProgressBar(98, response.message);
                        checkFileStatusOnVectorStoreJs(newFileId,response.last_sync,dataType,true);
                    } else {
                        updateProgressBar(72, response.message, true);
                    }

                });
            }
               
            function getVectorStoreFiles(){

            let vectorStoreId = $("#buddybot_vector_store_id").val();

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

        echo '
        checkFileStatusOnVectorStoreJs();
        function checkFileStatusOnVectorStoreJs(newFileId,last_sync,DataType="", hidebar=false) {
        $(".list-group-item").each(function(){
            let listItem = $(this);
            let data_type = listItem.attr("data-buddybot-type");
            let dataType = DataType || data_type;
            let fileId = newFileId || listItem.attr("data-buddybot-remote_file_id");
            let vectorStoreId =  $("#buddybot_vector_store_id").val();

                const data = {
                    "action": "checkFileStatusOnVectorStoreJs",
                    "file_id": fileId,
                    "vectorstore_id": vectorStoreId,
                    "last_sync": last_sync,
                    "data_type": dataType,
                    "nonce": "' . esc_js($nonce) . '"
                };
      
                $.post(ajaxurl, data, function(response) {

                    if(hidebar) {
                        hideProgressBar();
                        setTimeout(function() {
                            updateProgressBar(0, "' . esc_js(__('Sync processing...', 'buddybot-ai-custom-ai-assistant-and-chat-agent')) . '");
                        }, 1000);
                        syncBtnStop(dataType);
                    }
                    response = JSON.parse(response);
                    listItem.find(".buddybot-remote-file-status"+dataType).html(response.message); 
                });

        });
        }
        ';
    }
}
