<div class="layotter-modal-confirm layotter-modal-prompt">
    <div class="layotter-modal-confirm-message">
        <p>
            {{ prompt.message }}
        </p>
        <p>
            <input id="layotter-modal-prompt-input" type="text" value="{{ prompt.initialValue }}">
        </p>
    </div>
    <div class="layotter-modal-confirm-buttons">
        <span class="button button-primary button-large" ng-click="prompt.okAction()">{{ prompt.okText }}</span>
        <span class="button button-large" ng-click="prompt.cancelAction()">{{ prompt.cancelText }}</span>
    </div>
</div>