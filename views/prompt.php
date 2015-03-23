<div class="eddditor-modal-confirm eddditor-modal-prompt">
    <div class="eddditor-modal-confirm-message">
        <p>
            {{ prompt.message }}
        </p>
        <p>
            <input id="eddditor-modal-prompt-input" type="text" value="{{ prompt.initialValue }}">
        </p>
    </div>
    <div class="eddditor-modal-confirm-buttons">
        <span class="button button-primary button-large" ng-click="prompt.okAction()">{{ prompt.okText }}</span>
        <span class="button button-large" ng-click="prompt.cancelAction()">{{ prompt.cancelText }}</span>
    </div>
</div>