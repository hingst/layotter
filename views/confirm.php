<div class="layotter-modal-confirm">
    <div class="layotter-modal-confirm-message">
        <p>
            {{ confirm.message }}
        </p>
    </div>
    <div class="layotter-modal-confirm-buttons">
        <span class="button button-danger button-large" ng-click="confirm.okAction()">{{ confirm.okText }}</span>
        <span class="button button-large" ng-click="confirm.cancelAction()">{{ confirm.cancelText }}</span>
    </div>
</div>