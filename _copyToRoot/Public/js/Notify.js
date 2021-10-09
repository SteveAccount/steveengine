"use strict";

class Notify{
    notifyContainer;

    constructor(){
        this.notifyContainer = $("<div class='notifyContainer'></div>");
        $("body").append(this.notifyContainer);
    }

    info(message, visibleTime = 3){
        this.#show("notifyInfo", message, visibleTime);
    }

    success(message, visibleTime = 3){
        this.#show("notifySuccess", message, visibleTime);
    }

    warning(message, visibleTime = 3){
        this.#show("notifyWarning", message, visibleTime);
    }

    #show(notifyClass, message, visibleTime){
        let notifyBox   = $("<div class='notifyBox'>" + message + "</div>");
        notifyBox.addClass(notifyClass)
        this.notifyContainer.append(notifyBox);
        notifyBox.slideDown(500);
        setTimeout(() => {
            notifyBox.slideUp(500);
        }, visibleTime * 1000)
    }
}